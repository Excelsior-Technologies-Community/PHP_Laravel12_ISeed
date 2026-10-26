<?php

namespace App\Http\Controllers;

use App\Models\SeederHistory;
use App\Services\SeederDataMasker;
use App\Services\SeederDiffService;
use App\Services\TableDependencyResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SeederController extends Controller
{
    protected TableDependencyResolver $dependencyResolver;
    protected SeederDataMasker $dataMasker;
    protected SeederDiffService $diffService;

    public function __construct(
        TableDependencyResolver $dependencyResolver,
        SeederDataMasker $dataMasker,
        SeederDiffService $diffService
    ) {
        $this->dependencyResolver = $dependencyResolver;
        $this->dataMasker = $dataMasker;
        $this->diffService = $diffService;
    }

    /**
     * Seeder Dashboard View.
     */
    public function index()
    {
        $tables = $this->dependencyResolver->getTables();
        $histories = SeederHistory::latest()->paginate(20);

        // Stats
        $stats = [
            'total_tables' => count($tables),
            'total_generated' => SeederHistory::count(),
            'total_executed' => SeederHistory::where('status', 'executed')->count(),
            'masked_seeders' => SeederHistory::where('masking_applied', true)->count(),
            'total_records_seeded' => SeederHistory::sum('records'),
        ];

        return view('seeders.dashboard', compact('tables', 'histories', 'stats'));
    }

    /**
     * Batch or Single Seeder Generation with Filtering & Masking.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'tables' => 'nullable|array',
            'tables.*' => 'string',
            'single_table' => 'nullable|string',
            'max_records' => 'nullable|integer|min:1',
            'skip_records' => 'nullable|integer|min:0',
            'where_clause' => 'nullable|string|max:255',
            'exclude_columns' => 'nullable|string|max:255',
            'chunk_size' => 'nullable|integer|min:1|max:10000',
            'order_by' => 'nullable|string|max:100',
            'order_direction' => 'nullable|in:asc,desc,ASC,DESC',
            'mask_sensitive_data' => 'nullable|boolean',
            'seed_all_tables' => 'nullable|boolean',
        ]);

        // Determine candidate tables
        $allTablesData = $this->dependencyResolver->getTables();
        $allTableNames = array_column($allTablesData, 'name');

        if ($request->boolean('seed_all_tables')) {
            $selectedTables = $allTableNames;
        } elseif (!empty($request->tables)) {
            $selectedTables = (array) $request->tables;
        } elseif (!empty($request->single_table)) {
            $selectedTables = [$request->single_table];
        } else {
            return back()->with('error', 'Please select at least one database table to generate seeders.');
        }

        // Sort tables topologically by foreign-key dependencies (parents first)
        $orderedTables = $this->dependencyResolver->sortTablesByDependency($selectedTables);

        $batchId = (string) Str::uuid();
        $generatedCount = 0;
        $totalRecords = 0;

        foreach ($orderedTables as $table) {
            $seederName = ucfirst(Str::camel($table)) . 'TableSeeder';
            $filePath = database_path("seeders/{$seederName}.php");

            // Capture prior code if existing, for live diff inspection
            $previousCode = File::exists($filePath) ? File::get($filePath) : null;

            // Prepare artisan options
            $options = [
                'tables' => $table,
                '--force' => true,
                '--dumpauto' => false,
            ];

            // 1. Data Slicing: Limit & Offset
            if ($request->filled('max_records')) {
                $options['--max'] = (int) $request->max_records;
            }
            if ($request->filled('skip_records')) {
                $options['--skip'] = (int) $request->skip_records;
            }

            // 2. Data Slicing: Where Clause & Ordering
            if ($request->filled('where_clause')) {
                $options['--where'] = $request->where_clause;
            }
            if ($request->filled('order_by')) {
                $options['--orderby'] = $request->order_by;
                $options['--direction'] = strtolower($request->get('order_direction', 'desc'));
            }

            // 3. Large Table Chunking
            if ($request->filled('chunk_size')) {
                $options['--chunksize'] = (int) $request->chunk_size;
            }

            // 4. Column Exclusion
            if ($request->filled('exclude_columns')) {
                $options['--exclude'] = $request->exclude_columns;
            }

            // Execute Native Artisan Call
            Artisan::call('iseed', $options);

            // 5. Sensitive Data Masking (GDPR Anonymization)
            $maskApplied = false;
            if ($request->boolean('mask_sensitive_data') && File::exists($filePath)) {
                $maskApplied = $this->dataMasker->maskSeederFile($filePath);
            }

            // Calculate metrics
            $records = 0;
            try {
                $records = DB::table($table)->count();
                if ($request->filled('max_records') && $records > (int) $request->max_records) {
                    $records = (int) $request->max_records;
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $fileSizeKb = File::exists($filePath) ? round(File::size($filePath) / 1024, 2) : 0;

            SeederHistory::create([
                'batch_id' => $batchId,
                'table_name' => $table,
                'seeder_name' => $seederName,
                'file_path' => "database/seeders/{$seederName}.php",
                'records' => $records,
                'file_size_kb' => $fileSizeKb,
                'status' => 'generated',
                'masking_applied' => $maskApplied,
                'chunk_size' => $request->filled('chunk_size') ? (int) $request->chunk_size : null,
                'options' => array_filter([
                    'max' => $request->max_records,
                    'skip' => $request->skip_records,
                    'where' => $request->where_clause,
                    'exclude' => $request->exclude_columns,
                    'order_by' => $request->order_by,
                ]),
                'previous_code' => $previousCode,
            ]);

            $generatedCount++;
            $totalRecords += $records;
        }

        $message = sprintf(
            'Successfully generated %d seeder(s) covering %s record(s) in dependency order.%s',
            $generatedCount,
            number_format($totalRecords),
            $request->boolean('mask_sensitive_data') ? ' 🛡️ GDPR Privacy Masking was applied.' : ''
        );

        return back()->with('success', $message);
    }

    /**
     * Execute Seeder with Latency Tracking.
     */
    public function run(Request $request)
    {
        $request->validate([
            'seeder' => 'required|string',
        ]);

        $seeder = $request->seeder;
        $startTime = microtime(true);

        try {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Seeder execution failed: ' . $e->getMessage());
        }

        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        SeederHistory::where('seeder_name', $seeder)
            ->latest()
            ->first()
            ?->update([
                'status' => 'executed',
                'execution_time_ms' => $executionTimeMs,
            ]);

        return back()->with(
            'success',
            sprintf('Seeder "%s" executed successfully in %d ms.', $seeder, $executionTimeMs)
        );
    }

    /**
     * In-Browser Code Inspector (JSON endpoint for modal/studio).
     */
    public function viewCode(string $name): JsonResponse
    {
        $filePath = database_path("seeders/{$name}.php");

        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => "Seeder file [{$name}.php] not found on disk.",
            ], 404);
        }

        $code = File::get($filePath);
        $lines = count(explode("\n", $code));
        $sizeKb = round(File::size($filePath) / 1024, 2);

        return response()->json([
            'success' => true,
            'name' => $name,
            'file_name' => "{$name}.php",
            'file_path' => "database/seeders/{$name}.php",
            'size_kb' => $sizeKb,
            'lines' => $lines,
            'code' => $code,
            'last_modified' => date('Y-m-d H:i:s', File::lastModified($filePath)),
        ]);
    }

    /**
     * Live Diff Inspector (compares current seeder code with previous version).
     */
    public function diff(int $id): JsonResponse
    {
        $history = SeederHistory::findOrFail($id);
        $filePath = $history->getSeederFilePath();

        $currentCode = File::exists($filePath) ? File::get($filePath) : '';
        $previousCode = $history->previous_code;

        if (empty($previousCode)) {
            return response()->json([
                'success' => false,
                'message' => 'No previous version recorded for this generation entry.',
                'current_code' => $currentCode,
            ]);
        }

        $diff = $this->diffService->computeDiff($previousCode, $currentCode);

        return response()->json([
            'success' => true,
            'seeder_name' => $history->seeder_name,
            'table_name' => $history->table_name,
            'diff' => $diff,
        ]);
    }

    /**
     * In-Browser Quick Edit & Save.
     */
    public function updateCode(Request $request): JsonResponse
    {
        $request->validate([
            'seeder_name' => 'required|string',
            'code' => 'required|string',
        ]);

        $seederName = $request->seeder_name;
        $filePath = database_path("seeders/{$seederName}.php");

        if (!File::exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        // Quick syntax check using php -l via temp file
        $tempPath = tempnam(sys_get_temp_dir(), 'seeder_check_');
        File::put($tempPath, $request->code);
        $lintOutput = shell_exec("php -l " . escapeshellarg($tempPath));
        @unlink($tempPath);

        if ($lintOutput && !str_contains($lintOutput, 'No syntax errors detected')) {
            return response()->json([
                'success' => false,
                'message' => 'PHP Syntax Error: ' . trim($lintOutput),
            ], 422);
        }

        File::put($filePath, $request->code);
        $newSizeKb = round(File::size($filePath) / 1024, 2);

        SeederHistory::where('seeder_name', $seederName)
            ->latest()
            ->first()
            ?->update([
                'file_size_kb' => $newSizeKb,
            ]);

        return response()->json([
            'success' => true,
            'message' => "Seeder [{$seederName}.php] saved successfully.",
            'size_kb' => $newSizeKb,
        ]);
    }

    /**
     * Download Generated Seeder File.
     */
    public function download($name)
    {
        $path = database_path("seeders/{$name}.php");

        if (File::exists($path)) {
            return response()->download($path);
        }

        return back()->with('error', 'Seeder file not found.');
    }

    /**
     * Delete Seeder File and History Record.
     */
    public function destroy(int $id)
    {
        $history = SeederHistory::findOrFail($id);
        $filePath = $history->getSeederFilePath();

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $history->delete();

        return back()->with('success', 'Seeder file and history entry deleted.');
    }
}

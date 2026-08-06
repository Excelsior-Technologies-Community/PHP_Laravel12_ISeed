<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeederHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class SeederController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');

        $tables = collect($tables)
            ->map(function ($table) {
                return array_values((array) $table)[0];
            })
            ->filter(function ($table) {
                return !in_array($table, [
                    'migrations',
                    'sessions',
                    'jobs',
                    'job_batches',
                    'failed_jobs',
                    'password_reset_tokens',
                    'cache',
                    'cache_locks',
                    'seeder_histories',
                ]);
            })
            ->values();

        $histories = SeederHistory::latest()->get();

        return view(
            'seeders.dashboard',
            compact(
                'tables',
                'histories'
            )
        );
    }

    public function generate(Request $request)
    {

        $table = $request->table;


        exec(
            "php artisan iseed " . $table . " --force"
        );


        $seederName =
            ucfirst($table) . "TableSeeder";


        $records = DB::table($table)->count();



        SeederHistory::create([

            'table_name' => $table,

            'seeder_name' => $seederName,

            'records' => $records,

            'status' => 'generated'

        ]);

        return back()
            ->with(
                'success',
                'Seeder generated successfully'
            );
    }

    public function run(Request $request)
    {
        $seeder = $request->seeder;

        Artisan::call('db:seed', [
            '--class' => $seeder,
        ]);

        SeederHistory::where('seeder_name', $seeder)
            ->update([
                'status' => 'executed'
            ]);

        return back()->with(
            'success',
            'Seeder executed successfully'
        );
    }
    public function download($name)
    {

        $path =
            database_path(
                "seeders/" . $name . ".php"
            );


        if (File::exists($path)) {
            return response()->download($path);
        }


        return back();
    }
}

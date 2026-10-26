<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableDependencyResolver
{
    protected array $ignoredTables = [
        'migrations',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'cache',
        'cache_locks',
        'seeder_histories',
    ];

    /**
     * Get all candidate tables for seeding with metadata.
     */
    public function getTables(): array
    {
        $allTables = Schema::getTableListing();
        $filtered = array_values(array_diff($allTables, $this->ignoredTables));

        $dependencies = $this->getDependencyGraph($filtered);
        $sorted = $this->topologicalSort($filtered, $dependencies);

        $result = [];
        foreach ($sorted as $table) {
            $count = 0;
            try {
                $count = DB::table($table)->count();
            } catch (\Throwable $e) {
                // Table might be inaccessible or empty
            }

            $columns = [];
            try {
                $columns = Schema::getColumnListing($table);
            } catch (\Throwable $e) {
                // ignore
            }

            $dependsOn = $dependencies[$table] ?? [];

            $result[] = [
                'name' => $table,
                'count' => $count,
                'columns' => $columns,
                'depends_on' => $dependsOn,
                'has_dependencies' => !empty($dependsOn),
            ];
        }

        return $result;
    }

    /**
     * Build directed graph of foreign key dependencies.
     */
    public function getDependencyGraph(array $tables): array
    {
        $graph = array_fill_keys($tables, []);
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            try {
                $dbName = DB::getDatabaseName();
                $fkRows = DB::select("
                    SELECT TABLE_NAME, REFERENCED_TABLE_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = ? 
                      AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$dbName]);

                foreach ($fkRows as $row) {
                    $tbl = $row->TABLE_NAME;
                    $ref = $row->REFERENCED_TABLE_NAME;
                    if (in_array($tbl, $tables) && in_array($ref, $tables) && $tbl !== $ref) {
                        $graph[$tbl][] = $ref;
                    }
                }
            } catch (\Throwable $e) {
                // fallback
            }
        } elseif ($driver === 'sqlite') {
            try {
                foreach ($tables as $tbl) {
                    $fkList = DB::select("PRAGMA foreign_key_list(" . $tbl . ")");
                    foreach ($fkList as $fk) {
                        $ref = $fk->table;
                        if (in_array($ref, $tables) && $ref !== $tbl) {
                            $graph[$tbl][] = $ref;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // fallback
            }
        }

        foreach ($graph as $k => $deps) {
            $graph[$k] = array_unique($deps);
        }

        return $graph;
    }

    /**
     * Topological sort so independent tables come before child tables.
     */
    public function topologicalSort(array $tables, array $dependencies): array
    {
        $visited = [];
        $temp = [];
        $order = [];

        $visit = function ($node) use (&$visit, &$visited, &$temp, &$order, $dependencies) {
            if (isset($temp[$node])) {
                // Circular dependency detected, skip to avoid infinite recursion
                return;
            }
            if (!isset($visited[$node])) {
                $temp[$node] = true;
                foreach ($dependencies[$node] ?? [] as $dep) {
                    $visit($dep);
                }
                $temp[$node] = false;
                $visited[$node] = true;
                $order[] = $node;
            }
        };

        foreach ($tables as $table) {
            if (!isset($visited[$table])) {
                $visit($table);
            }
        }

        return array_values(array_unique($order));
    }

    /**
     * Sort an arbitrary user-submitted list of tables by dependency order.
     */
    public function sortTablesByDependency(array $requestedTables): array
    {
        $deps = $this->getDependencyGraph($requestedTables);
        return $this->topologicalSort($requestedTables, $deps);
    }
}

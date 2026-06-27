<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearData extends Command
{
    protected $signature   = 'data:clear {--force : Skip confirmation prompt}';
    protected $description = 'Hapus semua data operasional kecuali users dan stations';

    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Ini akan menghapus SEMUA data kecuali users dan stations. Lanjutkan?')) {
                $this->info('Dibatalkan.');
                return 0;
            }
        }

        $tables = [
            'activity_logs', 'notifications',
            'qc_checklist_items', 'qc_inspections',
            'cost_entries', 'budgets',
            'material_allocations', 'material_receipts',
            'purchase_order_items', 'purchase_orders',
            'handover_items', 'handovers',
            'cutting_bundles', 'cutting_plans',
            'wip_entries',
            'production_order_items', 'production_orders',
            'bom_items',
            'skus', 'series', 'products',
            'raw_materials', 'suppliers',
            'sewing_locations',
            'colors', 'sizes',
        ];

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        foreach ($tables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->line("<fg=yellow>Skip (tidak ada):</> {$table}");
                continue;
            }
            DB::table($table)->delete();
            $this->line("<fg=green>Cleared:</> {$table}");
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->newLine();
        $this->info('Selesai. Users dan stations tetap utuh.');
        $this->line('Users   : ' . DB::table('users')->count());
        $this->line('Stations: ' . DB::table('stations')->count());

        return 0;
    }
}

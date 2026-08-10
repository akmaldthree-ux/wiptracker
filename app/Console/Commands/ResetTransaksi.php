<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetTransaksi extends Command
{
    protected $signature   = 'app:reset-transaksi {--force : Jalankan tanpa konfirmasi interaktif}';
    protected $description = 'Hapus semua data transaksi (order, WIP, handover, procurement, dsb.) dan pertahankan master data.';

    // Urutan harus memperhatikan foreign key — tabel anak dulu baru induk
    private array $tables = [
        'activity_logs',
        'notifications',
        'rework_repurposes',
        'second_stock',
        'qc_inspections',
        'cutting_bundles',
        'cutting_plans',
        'handover_items',
        'handovers',
        'wip_entries',
        'order_station_deadlines',
        'production_order_items',
        'material_allocations',
        'material_requirements',
        'cost_entries',
        'budgets',
        'purchase_orders',
        'material_receipts',
        'production_orders',
    ];

    public function handle(): int
    {
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->warn('  RESET TRANSAKSI — Data berikut akan dihapus:');
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        foreach ($this->tables as $t) {
            $this->line("  • {$t}");
        }
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  Master data (produk, SKU, stasiun, user, dsb.) TIDAK tersentuh.');
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if (!$this->option('force')) {
            if (!$this->confirm('Lanjutkan reset? Tindakan ini tidak dapat dibatalkan.', false)) {
                $this->info('Reset dibatalkan.');
                return self::SUCCESS;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $total = 0;
        foreach ($this->tables as $table) {
            try {
                $count = DB::table($table)->count();
                DB::table($table)->truncate();
                $this->line("  ✓ {$table} ({$count} baris dihapus)");
                $total += $count;
            } catch (\Exception $e) {
                $this->error("  ✗ {$table}: " . $e->getMessage());
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->newLine();
        $this->info("Reset selesai. Total {$total} baris dihapus.");
        return self::SUCCESS;
    }
}

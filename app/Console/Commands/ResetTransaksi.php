<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ResetTransaksi extends Command
{
    protected $signature = 'app:reset-transaksi
                            {--force : Jalankan tanpa konfirmasi interaktif}';

    protected $description = 'Hapus seluruh transaksi, nolkan stok bahan baku, dan pertahankan master data.';

    /**
     * Urutan anak ke induk. Foreign key sengaja tetap aktif agar tabel relasi
     * yang terlewat membuat seluruh reset gagal dan di-rollback.
     *
     * @var list<string>
     */
    private array $transactionTables = [
        'activity_logs',
        'notifications',
        'rework_repurposes',
        'second_stock',
        'qc_checklist_items',
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
        'purchase_order_items',
        'purchase_orders',
        'material_receipts',
        'production_orders',
    ];

    /** @var list<string> */
    private array $preservedMasterTables = [
        'users dan hak akses',
        'stations dan sewing_locations',
        'products, series, colors, sizes, dan skus',
        'suppliers dan raw_materials',
        'bom_items',
    ];

    public function handle(): int
    {
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->warn('  RESET SELURUH TRANSAKSI');
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('  • Seluruh data transaksi akan dihapus.');
        $this->line('  • WIP, reject, rework, dan second stock akan dihapus.');
        $this->line('  • Stok semua bahan baku akan diubah menjadi 0.');
        $this->line('  • Foto handover, reject, dan QC terkait akan dihapus.');
        $this->newLine();
        $this->info('  Data master yang dipertahankan:');
        foreach ($this->preservedMasterTables as $table) {
            $this->line("  ✓ {$table}");
        }
        $this->warn('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if (! $this->option('force')
            && ! $this->confirm('Backup database sudah dibuat dan reset boleh dilanjutkan?', false)) {
            $this->info('Reset dibatalkan.');

            return self::SUCCESS;
        }

        $missingTables = collect($this->transactionTables)
            ->push('raw_materials')
            ->reject(fn (string $table): bool => Schema::hasTable($table));

        if ($missingTables->isNotEmpty()) {
            $this->error('Reset dibatalkan karena tabel berikut tidak ditemukan: '.$missingTables->implode(', '));

            return self::FAILURE;
        }

        $photoPaths = $this->transactionPhotoPaths();
        $deletedRows = [];

        try {
            DB::transaction(function () use (&$deletedRows): void {
                foreach ($this->transactionTables as $table) {
                    $deletedRows[$table] = DB::table($table)->delete();
                }

                $deletedRows['raw_materials.current_stock'] = DB::table('raw_materials')
                    ->where('current_stock', '!=', 0)
                    ->update(['current_stock' => 0]);
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Reset gagal. Seluruh perubahan database telah di-rollback.');
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $failedPhotos = $this->deleteTransactionPhotos($photoPaths);

        foreach ($deletedRows as $table => $count) {
            $label = $table === 'raw_materials.current_stock'
                ? 'stok bahan baku dinolkan'
                : "{$table} dihapus";
            $this->line("  ✓ {$label}: {$count} baris");
        }

        if ($failedPhotos !== []) {
            $this->warn('Reset database selesai, tetapi ada '.count($failedPhotos).' foto yang gagal dihapus.');
            foreach ($failedPhotos as $path) {
                $this->line("  • {$path}");
            }

            return self::FAILURE;
        } else {
            $this->line('  ✓ Foto transaksi dihapus: '.count($photoPaths).' file');
        }

        $this->newLine();
        $this->info('Reset transaksi selesai. Data master tetap utuh dan stok bahan baku sekarang 0.');

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function transactionPhotoPaths(): array
    {
        return collect()
            ->merge(DB::table('handovers')->pluck('photo_sent'))
            ->merge(DB::table('handovers')->pluck('photo_received'))
            ->merge(DB::table('handover_items')->pluck('photo_reject'))
            ->merge(DB::table('qc_inspections')->pluck('photo_evidence'))
            ->filter()
            ->map(fn (string $path): string => preg_replace('#^/?storage/#', '', $path) ?? $path)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $paths
     * @return list<string>
     */
    private function deleteTransactionPhotos(array $paths): array
    {
        $failed = [];

        foreach ($paths as $path) {
            try {
                if (Storage::disk('public')->exists($path)
                    && ! Storage::disk('public')->delete($path)) {
                    $failed[] = $path;
                }
            } catch (Throwable $exception) {
                report($exception);
                $failed[] = $path;
            }
        }

        return $failed;
    }
}

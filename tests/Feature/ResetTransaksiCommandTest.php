<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResetTransaksiCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_transactions_zeros_stock_and_preserves_master_data(): void
    {
        Storage::fake('public');

        $stationId = DB::table('stations')->insertGetId([
            'name' => 'Cutting',
            'code' => 'CUT',
        ]);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => json_encode(['admin']),
            'station_id' => $stationId,
        ]);
        $productId = DB::table('products')->insertGetId([
            'code' => 'PRD-1',
            'name' => 'Produk Tetap',
        ]);
        $colorId = DB::table('colors')->insertGetId([
            'name' => 'Hitam',
            'code' => 'BLK',
        ]);
        $sizeId = DB::table('sizes')->insertGetId([
            'name' => 'M',
        ]);
        $seriesId = DB::table('series')->insertGetId([
            'name' => 'Series Tetap',
            'code' => 'SER-1',
            'product_id' => $productId,
        ]);
        $skuId = DB::table('skus')->insertGetId([
            'product_id' => $productId,
            'series_id' => $seriesId,
            'color_id' => $colorId,
            'size_id' => $sizeId,
            'sku_code' => 'SKU-1',
        ]);
        $supplierId = DB::table('suppliers')->insertGetId([
            'name' => 'Supplier Tetap',
            'code' => 'SUP-1',
        ]);
        $materialId = DB::table('raw_materials')->insertGetId([
            'code' => 'MAT-1',
            'name' => 'Bahan Tetap',
            'unit' => 'meter',
            'current_stock' => 125.5,
            'supplier_id' => $supplierId,
        ]);
        DB::table('bom_items')->insert([
            'product_id' => $productId,
            'raw_material_id' => $materialId,
            'qty_per_unit' => 1.25,
        ]);

        $orderId = DB::table('production_orders')->insertGetId([
            'order_no' => 'ORD-OLD',
            'product_id' => $productId,
            'series_id' => $seriesId,
            'target_date' => now()->toDateString(),
            'created_by' => $userId,
        ]);
        DB::table('production_order_items')->insert([
            'production_order_id' => $orderId,
            'sku_id' => $skuId,
            'target_qty' => 10,
        ]);
        DB::table('wip_entries')->insert([
            'production_order_id' => $orderId,
            'sku_id' => $skuId,
            'station_id' => $stationId,
            'qty_in' => 10,
            'input_date' => now()->toDateString(),
            'created_by' => $userId,
        ]);

        $handoverId = DB::table('handovers')->insertGetId([
            'handover_no' => 'HO-OLD',
            'production_order_id' => $orderId,
            'from_station_id' => $stationId,
            'to_station_id' => $stationId,
            'initiated_by' => $userId,
            'photo_sent' => 'storage/handovers/sent/sent.jpg',
            'photo_received' => 'storage/handovers/received/received.jpg',
        ]);
        $handoverItemId = DB::table('handover_items')->insertGetId([
            'handover_id' => $handoverId,
            'sku_id' => $skuId,
            'qty_sent' => 10,
            'photo_reject' => 'storage/handovers/reject/reject.jpg',
        ]);
        DB::table('second_stock')->insert([
            'production_order_id' => $orderId,
            'sku_id' => $skuId,
            'from_station_id' => $stationId,
            'handover_item_id' => $handoverItemId,
            'qty' => 2,
            'created_by' => $userId,
        ]);

        $inspectionId = DB::table('qc_inspections')->insertGetId([
            'production_order_id' => $orderId,
            'handover_id' => $handoverId,
            'inspector_id' => $userId,
            'inspected_at' => now(),
            'photo_evidence' => 'storage/qc/evidence.jpg',
        ]);
        DB::table('qc_checklist_items')->insert([
            'qc_inspection_id' => $inspectionId,
            'checklist_item' => 'Contoh pemeriksaan',
        ]);

        $purchaseOrderId = DB::table('purchase_orders')->insertGetId([
            'po_no' => 'PO-OLD',
            'supplier_id' => $supplierId,
            'order_date' => now()->toDateString(),
            'created_by' => $userId,
        ]);
        DB::table('purchase_order_items')->insert([
            'purchase_order_id' => $purchaseOrderId,
            'raw_material_id' => $materialId,
            'qty_ordered' => 100,
            'unit_price' => 10000,
            'total_price' => 1000000,
        ]);
        DB::table('material_receipts')->insert([
            'raw_material_id' => $materialId,
            'supplier_id' => $supplierId,
            'qty' => 100,
            'unit_price' => 10000,
            'total_price' => 1000000,
            'receipt_date' => now()->toDateString(),
        ]);

        foreach ([
            'handovers/sent/sent.jpg',
            'handovers/received/received.jpg',
            'handovers/reject/reject.jpg',
            'qc/evidence.jpg',
        ] as $path) {
            Storage::disk('public')->put($path, 'photo');
        }

        $this->artisan('app:reset-transaksi', ['--force' => true])
            ->assertSuccessful();

        foreach ([
            'production_orders',
            'production_order_items',
            'wip_entries',
            'handovers',
            'handover_items',
            'second_stock',
            'qc_inspections',
            'qc_checklist_items',
            'purchase_orders',
            'purchase_order_items',
            'material_receipts',
        ] as $table) {
            $this->assertDatabaseCount($table, 0);
        }

        foreach ([
            'users',
            'stations',
            'products',
            'series',
            'colors',
            'sizes',
            'skus',
            'suppliers',
            'raw_materials',
            'bom_items',
        ] as $table) {
            $this->assertDatabaseCount($table, 1);
        }

        $this->assertDatabaseHas('raw_materials', [
            'id' => $materialId,
            'current_stock' => 0,
        ]);

        Storage::disk('public')->assertMissing('handovers/sent/sent.jpg');
        Storage::disk('public')->assertMissing('handovers/received/received.jpg');
        Storage::disk('public')->assertMissing('handovers/reject/reject.jpg');
        Storage::disk('public')->assertMissing('qc/evidence.jpg');
    }
}

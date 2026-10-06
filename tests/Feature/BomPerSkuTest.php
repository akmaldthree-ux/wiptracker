<?php

namespace Tests\Feature;

use App\Models\BomItem;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderItem;
use App\Models\RawMaterial;
use App\Models\Series;
use App\Models\Size;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BomPerSkuTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculator_uses_the_selected_sku_bom(): void
    {
        $data = $this->createCatalog();
        $this->createBom($data['sku_s'], $data['material'], 2.0);
        $this->createBom($data['sku_l'], $data['material'], 3.0);

        $this->actingAs($data['admin'])
            ->get(route('bom.index'))
            ->assertOk()
            ->assertSee('Bill of Materials (BOM) per SKU')
            ->assertSee($data['sku_s']->sku_code)
            ->assertSee('Size S');

        $this->actingAs($data['admin'])
            ->getJson(route('api.bom.calculate', ['sku_id' => $data['sku_l']->id, 'qty' => 5]))
            ->assertOk()
            ->assertJsonFragment([
                'material' => 'Kain Utama',
                'qty_needed' => 15,
            ]);
    }

    public function test_procurement_aggregates_requirements_from_each_order_sku(): void
    {
        $data = $this->createCatalog();
        $this->createBom($data['sku_s'], $data['material'], 2.0);
        $this->createBom($data['sku_l'], $data['material'], 3.0);
        $order = $this->createOrder($data, [
            $data['sku_s']->id => 10,
            $data['sku_l']->id => 5,
        ]);

        $this->actingAs($data['admin'])
            ->get(route('procurement.show', $order))
            ->assertOk()
            ->assertSee('Berdasarkan komposisi 15 pcs dan BOM setiap SKU');

        $this->assertDatabaseHas('material_requirements', [
            'production_order_id' => $order->id,
            'raw_material_id' => $data['material']->id,
            'qty_needed' => 35,
        ]);
    }

    public function test_procurement_cannot_approve_an_order_with_a_missing_sku_bom(): void
    {
        $data = $this->createCatalog();
        $this->createBom($data['sku_s'], $data['material'], 2.0);
        $order = $this->createOrder($data, [
            $data['sku_s']->id => 10,
            $data['sku_l']->id => 5,
        ]);

        $this->actingAs($data['admin'])
            ->post(route('procurement.approve', $order))
            ->assertSessionHas('error', fn (string $message): bool => str_contains($message, $data['sku_l']->sku_code));

        $this->assertFalse($order->fresh()->materials_approved);
        $this->assertSame('100.00', $data['material']->fresh()->current_stock);
    }

    public function test_approval_reduces_stock_once_using_aggregated_sku_bom(): void
    {
        $data = $this->createCatalog();
        $this->createBom($data['sku_s'], $data['material'], 2.0);
        $this->createBom($data['sku_l'], $data['material'], 3.0);
        $order = $this->createOrder($data, [
            $data['sku_s']->id => 10,
            $data['sku_l']->id => 5,
        ]);

        $this->actingAs($data['admin'])->post(route('procurement.approve', $order))->assertSessionHas('success');
        $this->assertTrue($order->fresh()->materials_approved);
        $this->assertSame('65.00', $data['material']->fresh()->current_stock);

        $this->actingAs($data['admin'])->post(route('procurement.approve', $order))->assertSessionHas('error');
        $this->assertSame('65.00', $data['material']->fresh()->current_stock);
    }

    public function test_supervisor_can_copy_bom_to_other_skus_but_staff_cannot(): void
    {
        $data = $this->createCatalog();
        $this->createBom($data['sku_s'], $data['material'], 2.0);

        $staff = $this->createUser('staff_produksi');
        $this->actingAs($staff)
            ->post(route('bom.copy'), [
                'source_sku_id' => $data['sku_s']->id,
                'target_sku_ids' => [$data['sku_l']->id],
            ])
            ->assertForbidden();

        $this->actingAs($data['admin'])
            ->post(route('bom.copy'), [
                'source_sku_id' => $data['sku_s']->id,
                'target_sku_ids' => [$data['sku_l']->id],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bom_items', [
            'sku_id' => $data['sku_l']->id,
            'raw_material_id' => $data['material']->id,
            'qty_per_unit' => 2,
        ]);
    }

    /** @return array<string, mixed> */
    private function createCatalog(): array
    {
        $admin = $this->createUser('admin');
        $product = Product::create(['code' => 'PRD-001', 'name' => 'Jubah', 'is_active' => true]);
        $series = Series::create(['product_id' => $product->id, 'code' => 'SR-001', 'name' => 'Abaya', 'is_active' => true]);
        $color = Color::create(['code' => 'BLK', 'name' => 'Hitam', 'is_active' => true]);
        $sizeS = Size::create(['name' => 'S', 'sort_order' => 1, 'is_active' => true]);
        $sizeL = Size::create(['name' => 'L', 'sort_order' => 2, 'is_active' => true]);
        $skuS = Sku::create([
            'product_id' => $product->id,
            'series_id' => $series->id,
            'color_id' => $color->id,
            'size_id' => $sizeS->id,
            'sku_code' => 'SKU-ABAYA-BLK-S',
            'is_active' => true,
        ]);
        $skuL = Sku::create([
            'product_id' => $product->id,
            'series_id' => $series->id,
            'color_id' => $color->id,
            'size_id' => $sizeL->id,
            'sku_code' => 'SKU-ABAYA-BLK-L',
            'is_active' => true,
        ]);
        $material = RawMaterial::create([
            'code' => 'MAT-BLK',
            'name' => 'Kain Utama',
            'unit' => 'Yard',
            'category' => 'kain',
            'color' => 'Hitam',
            'min_stock' => 0,
            'current_stock' => 100,
            'unit_price' => 50000,
            'is_active' => true,
        ]);

        return compact('admin', 'product', 'series', 'skuS', 'skuL', 'material') + [
            'sku_s' => $skuS,
            'sku_l' => $skuL,
        ];
    }

    private function createBom(Sku $sku, RawMaterial $material, float $quantity): BomItem
    {
        return BomItem::create([
            'product_id' => $sku->product_id,
            'sku_id' => $sku->id,
            'raw_material_id' => $material->id,
            'qty_per_unit' => $quantity,
            'waste_percentage' => 0,
        ]);
    }

    /** @param array<int, int> $items */
    private function createOrder(array $data, array $items): ProductionOrder
    {
        $order = ProductionOrder::create([
            'order_no' => 'ORD-001',
            'product_id' => $data['product']->id,
            'series_id' => $data['series']->id,
            'target_date' => now()->addWeek(),
            'status' => 'draft',
            'created_by' => $data['admin']->id,
        ]);

        foreach ($items as $skuId => $quantity) {
            ProductionOrderItem::create([
                'production_order_id' => $order->id,
                'sku_id' => $skuId,
                'target_qty' => $quantity,
            ]);
        }

        return $order;
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => "{$role}@example.com",
            'password' => 'Rahasia!1234',
            'role' => $role,
            'roles' => [$role],
            'is_active' => true,
        ]);
    }
}

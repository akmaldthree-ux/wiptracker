<?php

namespace Tests\Feature;

use App\Models\BomItem;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearRawMaterialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_all_raw_materials_and_related_bom(): void
    {
        $admin = $this->createUser('admin');
        $product = Product::create([
            'code' => 'PRD-001',
            'name' => 'Produk Uji',
            'is_active' => true,
        ]);
        $material = $this->createMaterial('MAT-001');
        $this->createMaterial('MAT-002');

        BomItem::create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
            'qty_per_unit' => 1,
            'waste_percentage' => 0,
        ]);

        $this->actingAs($admin)
            ->get(route('bahan-baku.index'))
            ->assertOk()
            ->assertSee('Hapus Semua')
            ->assertSee(route('bahan-baku.clear-all'));

        $response = $this->actingAs($admin)->delete(route('bahan-baku.clear-all'), [
            'confirmation' => 'HAPUS SEMUA',
        ]);

        $response
            ->assertRedirect(route('bahan-baku.index'))
            ->assertSessionHas('success', '2 bahan baku beserta data terkait berhasil dihapus.');

        $this->assertDatabaseCount('raw_materials', 0);
        $this->assertDatabaseCount('bom_items', 0);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'raw_materials.clear_all',
        ]);
    }

    public function test_non_admin_cannot_delete_all_raw_materials(): void
    {
        $supervisor = $this->createUser('supervisor');
        $this->createMaterial('MAT-001');

        $this->actingAs($supervisor)
            ->get(route('bahan-baku.index'))
            ->assertOk()
            ->assertDontSee('Hapus Semua');

        $this->actingAs($supervisor)
            ->delete(route('bahan-baku.clear-all'), ['confirmation' => 'HAPUS SEMUA'])
            ->assertForbidden();

        $this->assertDatabaseCount('raw_materials', 1);
    }

    public function test_exact_confirmation_is_required(): void
    {
        $admin = $this->createUser('admin');
        $this->createMaterial('MAT-001');

        $this->actingAs($admin)
            ->from(route('bahan-baku.index'))
            ->delete(route('bahan-baku.clear-all'), ['confirmation' => 'hapus semua'])
            ->assertRedirect(route('bahan-baku.index'))
            ->assertSessionHasErrors('confirmation');

        $this->assertDatabaseCount('raw_materials', 1);
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

    private function createMaterial(string $code): RawMaterial
    {
        return RawMaterial::create([
            'code' => $code,
            'name' => "Bahan {$code}",
            'unit' => 'Yard',
            'category' => 'kain',
            'min_stock' => 0,
            'current_stock' => 0,
            'unit_price' => 0,
            'is_active' => true,
        ]);
    }
}

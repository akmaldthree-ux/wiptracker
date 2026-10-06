<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'raw_material_id']);
            $table->foreignId('sku_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['sku_id', 'raw_material_id']);
        });

        $legacyItems = DB::table('bom_items')->whereNull('sku_id')->get();

        foreach ($legacyItems as $item) {
            $skuIds = DB::table('skus')->where('product_id', $item->product_id)->pluck('id');

            foreach ($skuIds as $skuId) {
                DB::table('bom_items')->insert([
                    'product_id' => $item->product_id,
                    'sku_id' => $skuId,
                    'raw_material_id' => $item->raw_material_id,
                    'qty_per_unit' => $item->qty_per_unit,
                    'waste_percentage' => $item->waste_percentage,
                    'notes' => $item->notes,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]);
            }

            if ($skuIds->isNotEmpty()) {
                DB::table('bom_items')->where('id', $item->id)->delete();
            }
        }
    }

    public function down(): void
    {
        $groupedItems = DB::table('bom_items')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (object $item): string => "{$item->product_id}:{$item->raw_material_id}");

        foreach ($groupedItems as $items) {
            $keep = $items->first();

            DB::table('bom_items')->whereIn('id', $items->pluck('id'))->delete();
            DB::table('bom_items')->insert([
                'product_id' => $keep->product_id,
                'sku_id' => null,
                'raw_material_id' => $keep->raw_material_id,
                'qty_per_unit' => $keep->qty_per_unit,
                'waste_percentage' => $keep->waste_percentage,
                'notes' => $keep->notes,
                'created_at' => $keep->created_at,
                'updated_at' => $keep->updated_at,
            ]);
        }

        Schema::table('bom_items', function (Blueprint $table) {
            $table->dropUnique(['sku_id', 'raw_material_id']);
            $table->dropConstrainedForeignId('sku_id');
            $table->unique(['product_id', 'raw_material_id']);
        });
    }
};

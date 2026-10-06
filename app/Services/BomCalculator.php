<?php

namespace App\Services;

use App\Models\ProductionOrder;
use Illuminate\Support\Collection;

class BomCalculator
{
    /**
     * @return array{requirements: Collection<int, array<string, mixed>>, missing_skus: Collection<int, mixed>}
     */
    public function forOrder(ProductionOrder $order): array
    {
        $order->loadMissing(['items.sku.bomItems.rawMaterial']);

        $requirements = collect();
        $missingSkus = collect();

        foreach ($order->items->where('target_qty', '>', 0) as $item) {
            $sku = $item->sku;

            if (! $sku || $sku->bomItems->isEmpty()) {
                if ($sku) {
                    $missingSkus->push($sku);
                }

                continue;
            }

            foreach ($sku->bomItems as $bom) {
                $materialId = $bom->raw_material_id;
                $qty = $bom->getQtyNeeded((int) $item->target_qty);
                $current = $requirements->get($materialId, [
                    'raw_material' => $bom->rawMaterial,
                    'qty_needed' => 0.0,
                ]);
                $current['qty_needed'] += $qty;
                $requirements->put($materialId, $current);
            }
        }

        return [
            'requirements' => $requirements->map(function (array $requirement): array {
                $requirement['qty_needed'] = round($requirement['qty_needed'], 4);

                return $requirement;
            })->values(),
            'missing_skus' => $missingSkus->unique('id')->values(),
        ];
    }
}

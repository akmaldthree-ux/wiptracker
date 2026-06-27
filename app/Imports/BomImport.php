<?php
namespace App\Imports;

use App\Models\BomItem;
use App\Models\Product;
use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class BomImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $imported = 0;
    public array $notFound = [];

    public function model(array $row): ?BomItem
    {
        if (empty($row['kode_produk']) || empty($row['kode_material'])) return null;

        $product = Product::where('code', strtoupper(trim($row['kode_produk'])))->first();
        $material = RawMaterial::where('code', strtoupper(trim($row['kode_material'])))->first();

        if (!$product) {
            $this->notFound[] = "Produk '{$row['kode_produk']}' tidak ditemukan (baris diabaikan)";
            return null;
        }
        if (!$material) {
            $this->notFound[] = "Material '{$row['kode_material']}' tidak ditemukan (baris diabaikan)";
            return null;
        }

        $this->imported++;

        return BomItem::updateOrCreate(
            ['product_id' => $product->id, 'raw_material_id' => $material->id],
            [
                'qty_per_unit'      => floatval($row['qty_per_unit']),
                'waste_percentage'  => floatval($row['waste_pct'] ?? 0),
                'notes'             => trim($row['catatan'] ?? ''),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode_produk'   => 'required',
            'kode_material' => 'required',
            'qty_per_unit'  => 'required|numeric|min:0.0001',
        ];
    }
}

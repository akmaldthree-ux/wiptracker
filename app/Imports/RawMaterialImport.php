<?php
namespace App\Imports;

use App\Models\RawMaterial;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class RawMaterialImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): ?RawMaterial
    {
        if (empty($row['kode']) && empty($row['nama'])) return null;

        $supplier = null;
        if (!empty($row['kode_supplier'])) {
            $supplier = Supplier::where('code', strtoupper(trim($row['kode_supplier'])))->first();
        }

        return RawMaterial::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'          => trim($row['nama']),
                'unit'          => trim($row['satuan'] ?? 'pcs'),
                'category'      => trim($row['kategori'] ?? 'lainnya'),
                'color'         => trim($row['warna'] ?? ''),
                'current_stock' => floatval($row['stok_awal'] ?? 0),
                'min_stock'     => floatval($row['stok_minimum'] ?? 0),
                'unit_price'    => floatval($row['harga_satuan'] ?? 0),
                'supplier_id'   => $supplier?->id,
                'is_active'     => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode'    => 'required|string|max:50',
            'nama'    => 'required|string|max:255',
            'satuan'  => 'required|string|max:20',
        ];
    }
}

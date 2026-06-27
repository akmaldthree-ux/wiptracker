<?php
namespace App\Imports;

use App\Models\Series;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class SeriesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): ?Series
    {
        if (empty($row['kode']) && empty($row['nama'])) return null;

        $product = Product::where('code', strtoupper(trim($row['kode_produk'] ?? '')))->first();
        if (!$product) return null;

        return Series::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'       => trim($row['nama']),
                'product_id' => $product->id,
                'is_active'  => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode'        => 'required|string|max:20',
            'nama'        => 'required|string|max:100',
            'kode_produk' => 'required|string',
        ];
    }
}

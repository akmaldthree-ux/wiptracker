<?php
namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public array $errors = [];
    private int $row = 1;

    public function model(array $row): ?Product
    {
        $this->row++;
        if (empty($row['kode']) && empty($row['nama'])) return null;

        return Product::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'         => trim($row['nama']),
                'category'     => trim($row['kategori'] ?? ''),
                'description'  => trim($row['deskripsi'] ?? ''),
                'is_active'    => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode.required' => 'Kolom KODE wajib diisi',
            'nama.required' => 'Kolom NAMA wajib diisi',
        ];
    }
}

<?php
namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): ?Supplier
    {
        if (empty($row['nama'])) return null;

        return Supplier::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'           => trim($row['nama']),
                'phone'          => trim($row['telepon'] ?? ''),
                'email'          => trim($row['email'] ?? ''),
                'address'        => trim($row['alamat'] ?? ''),
                'contact_person' => trim($row['kontak_person'] ?? ''),
                'notes'          => trim($row['catatan'] ?? ''),
                'is_active'      => true,
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
}

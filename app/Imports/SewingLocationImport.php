<?php
namespace App\Imports;

use App\Models\SewingLocation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class SewingLocationImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row): ?SewingLocation
    {
        if (empty($row['kode']) && empty($row['nama'])) return null;

        return SewingLocation::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'        => trim($row['nama']),
                'address'     => !empty($row['alamat'])      ? trim($row['alamat'])      : null,
                'description' => !empty($row['deskripsi'])   ? trim($row['deskripsi'])   : null,
                'capacity'    => !empty($row['kapasitas'])    ? (int) $row['kapasitas']   : null,
                'is_active'   => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
        ];
    }
}

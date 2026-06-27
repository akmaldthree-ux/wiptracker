<?php
namespace App\Imports;

use App\Models\Color;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class ColorImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): ?Color
    {
        if (empty($row['kode']) && empty($row['nama'])) return null;

        return Color::updateOrCreate(
            ['code' => strtoupper(trim($row['kode']))],
            [
                'name'     => trim($row['nama']),
                'hex_code' => !empty($row['hex_code']) ? ltrim(trim($row['hex_code']), '#') : null,
                'is_active' => true,
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

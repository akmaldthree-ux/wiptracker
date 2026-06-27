<?php
namespace App\Imports;

use App\Models\Size;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class SizeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): ?Size
    {
        if (empty($row['nama'])) return null;

        return Size::updateOrCreate(
            ['name' => trim($row['nama']), 'type' => trim($row['tipe'] ?? 'general')],
            [
                'sort_order' => intval($row['urutan'] ?? 0),
                'is_active'  => true,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:50',
        ];
    }
}

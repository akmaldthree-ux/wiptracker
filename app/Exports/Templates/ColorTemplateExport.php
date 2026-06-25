<?php
namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColorTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['MRH', 'Merah', 'FF0000'],
            ['HJU', 'Hijau', '00FF00'],
            ['BRU', 'Biru', '0000FF'],
            ['PTH', 'Putih', 'FFFFFF'],
            ['HTM', 'Hitam', '000000'],
        ];
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'hex_code'];
    }

    public function title(): string { return 'Template Warna'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 30, 'C' => 15];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:6' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

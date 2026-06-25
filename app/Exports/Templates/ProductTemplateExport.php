<?php
namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['PRD001', 'Kemeja Pria Lengan Panjang', 'kemeja', 'Produk unggulan'],
            ['PRD002', 'Celana Chino', 'celana', ''],
        ];
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'kategori', 'deskripsi'];
    }

    public function title(): string { return 'Template Produk'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 35, 'C' => 20, 'D' => 40];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
            '2:3' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

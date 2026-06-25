<?php
namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['SUP001', 'PT Bahan Jaya', '0812-3456-7890', 'bahan@jaya.com', 'Jl. Industri No. 1, Jakarta', 'Budi Santoso', ''],
            ['SUP002', 'CV Tekstil Maju', '0821-9876-5432', '', 'Jl. Garmen No. 5, Bandung', 'Siti Rahayu', 'Supplier benang'],
        ];
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'telepon', 'email', 'alamat', 'kontak_person', 'catatan'];
    }

    public function title(): string { return 'Template Supplier'; }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 30, 'C' => 18, 'D' => 25, 'E' => 40, 'F' => 25, 'G' => 30];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:3' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

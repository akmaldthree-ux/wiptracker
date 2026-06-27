<?php
namespace App\Exports\Templates;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SeriesTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [new SeriesDataSheet(), new SeriesProductRefSheet()];
    }
}

class SeriesDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['SRS-2024-A', 'Series Batik 2024', 'BTK'],
            ['SRS-2024-B', 'Series Kemeja 2024', 'KMJ'],
        ];
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'kode_produk'];
    }

    public function title(): string { return 'Data Series'; }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 30, 'C' => 15];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:3' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

class SeriesProductRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return Product::where('is_active', true)->orderBy('name')
            ->get(['code', 'name', 'category'])
            ->map(fn($p) => [$p->code, $p->name, $p->category])
            ->toArray();
    }

    public function headings(): array
    {
        return ['kode_produk', 'nama_produk', 'kategori'];
    }

    public function title(): string { return 'Ref Produk'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 30, 'C' => 20];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '70AD47']]],
        ];
    }
}

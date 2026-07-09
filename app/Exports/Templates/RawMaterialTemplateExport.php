<?php
namespace App\Exports\Templates;

use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RawMaterialTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new RawMaterialDataSheet(),
            new RawMaterialRefSheet(),
        ];
    }
}

class RawMaterialDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        $existing = RawMaterial::with('supplier')->orderBy('code')->get()->map(fn($m) => [
            $m->code,
            $m->name,
            $m->unit,
            $m->category,
            $m->color ?? '',
            $m->current_stock,
            $m->min_stock,
            $m->unit_price,
            $m->supplier?->code ?? '',
        ])->toArray();

        // Fallback sample rows if DB is empty
        if (empty($existing)) {
            return [
                ['KAI001', 'Kain Katun 30s', 'meter', 'kain', 'Putih', 100, 20, 25000, 'SUP001'],
                ['BNG001', 'Benang Jahit Putih', 'rol', 'benang', '', 50, 10, 5000, 'SUP002'],
            ];
        }

        return $existing;
    }

    public function headings(): array
    {
        return ['kode', 'nama', 'satuan', 'kategori', 'warna', 'stok_awal', 'stok_minimum', 'harga_satuan', 'kode_supplier'];
    }

    public function title(): string { return 'Data Bahan Baku'; }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 30, 'C' => 10, 'D' => 12, 'E' => 12, 'F' => 12, 'G' => 14, 'H' => 14, 'I' => 15];
    }

    public function styles($sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:4' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

class RawMaterialRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return [['kain'], ['benang'], ['aksesoris'], ['lainnya']];
    }

    public function headings(): array
    {
        return ['nilai_kategori (isi salah satu)'];
    }

    public function title(): string { return 'Referensi'; }

    public function styles($sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}

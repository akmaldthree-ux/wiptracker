<?php
namespace App\Exports\Templates;

use App\Models\Product;
use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BomTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new BomDataSheet(),
            new BomProductRefSheet(),
            new BomMaterialRefSheet(),
        ];
    }
}

class BomDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['PRD001', 'KAI001', 1.5, 5, 'Untuk badan kemeja'],
            ['PRD001', 'BNG001', 0.1, 3, 'Benang jahit'],
            ['PRD001', 'AKS001', 0.5, 0, '5 kancing per pcs'],
        ];
    }

    public function headings(): array
    {
        return ['kode_produk', 'kode_material', 'qty_per_unit', 'waste_pct', 'catatan'];
    }

    public function title(): string { return 'Data BOM'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 15, 'C' => 14, 'D' => 12, 'E' => 40];
    }

    public function styles($sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:4' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

class BomProductRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return Product::select('code', 'name')->orderBy('code')->get()
            ->map(fn($p) => [$p->code, $p->name])->toArray();
    }

    public function headings(): array { return ['kode_produk', 'nama_produk']; }
    public function title(): string { return 'Referensi Produk'; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 35]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

class BomMaterialRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return RawMaterial::select('code', 'name', 'unit')->orderBy('code')->get()
            ->map(fn($m) => [$m->code, $m->name, $m->unit])->toArray();
    }

    public function headings(): array { return ['kode_material', 'nama_material', 'satuan']; }
    public function title(): string { return 'Referensi Material'; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 35, 'C' => 12]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

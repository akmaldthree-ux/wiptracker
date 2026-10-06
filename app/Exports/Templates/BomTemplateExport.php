<?php

namespace App\Exports\Templates;

use App\Models\BomItem;
use App\Models\RawMaterial;
use App\Models\Sku;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class BomTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new BomDataSheet,
            new BomSkuRefSheet,
            new BomMaterialRefSheet,
        ];
    }
}

class BomDataSheet implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return BomItem::query()
            ->with(['sku', 'rawMaterial'])
            ->whereNotNull('sku_id')
            ->orderBy('sku_id')
            ->get()
            ->map(fn (BomItem $item): array => [
                $item->sku->sku_code,
                $item->rawMaterial->code,
                $item->qty_per_unit,
                $item->waste_percentage,
                $item->notes,
            ])->all();
    }

    public function headings(): array
    {
        return ['kode_sku', 'kode_material', 'qty_per_unit', 'waste_pct', 'catatan'];
    }

    public function title(): string
    {
        return 'Data BOM';
    }

    public function columnWidths(): array
    {
        return ['A' => 28, 'B' => 18, 'C' => 14, 'D' => 12, 'E' => 40];
    }

    public function styles($sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}

class BomSkuRefSheet implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return Sku::query()
            ->with(['product', 'series', 'color', 'size'])
            ->orderBy('sku_code')
            ->get()
            ->map(fn (Sku $sku): array => [
                $sku->sku_code,
                $sku->product->name,
                $sku->series->name,
                $sku->color->name,
                $sku->size->name,
            ])->all();
    }

    public function headings(): array
    {
        return ['kode_sku', 'produk', 'series', 'warna', 'size'];
    }

    public function title(): string
    {
        return 'Referensi SKU';
    }

    public function columnWidths(): array
    {
        return ['A' => 28, 'B' => 28, 'C' => 32, 'D' => 18, 'E' => 12];
    }

    public function styles($sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}

class BomMaterialRefSheet implements FromArray, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    public function array(): array
    {
        return RawMaterial::query()
            ->select('code', 'name', 'unit', 'color')
            ->orderBy('code')
            ->get()
            ->map(fn (RawMaterial $material): array => [
                $material->code,
                $material->name,
                $material->color,
                $material->unit,
            ])->all();
    }

    public function headings(): array
    {
        return ['kode_material', 'nama_material', 'warna', 'satuan'];
    }

    public function title(): string
    {
        return 'Referensi Material';
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 35, 'C' => 20, 'D' => 12];
    }

    public function styles($sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}

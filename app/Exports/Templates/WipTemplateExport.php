<?php
namespace App\Exports\Templates;

use App\Models\ProductionOrder;
use App\Models\Station;
use App\Models\Color;
use App\Models\Size;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WipTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new WipDataSheet(),
            new WipOrderRefSheet(),
            new WipStationRefSheet(),
        ];
    }
}

class WipDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['ORD-2024-001', 'CUT', 'MRH', 'M', 50, 0, 0, date('Y-m-d'), ''],
            ['ORD-2024-001', 'CUT', 'MRH', 'L', 45, 0, 2, date('Y-m-d'), 'Ada reject cacat potong'],
        ];
    }

    public function headings(): array
    {
        return ['no_order', 'kode_stasiun', 'kode_warna', 'kode_ukuran', 'qty_in', 'qty_out', 'qty_reject', 'tanggal', 'catatan'];
    }

    public function title(): string { return 'Input WIP'; }

    public function columnWidths(): array
    {
        return ['A' => 16, 'B' => 15, 'C' => 13, 'D' => 13, 'E' => 10, 'F' => 10, 'G' => 12, 'H' => 14, 'I' => 35];
    }

    public function styles($sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
            '2:3' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EBF3FF']]],
        ];
    }
}

class WipOrderRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return ProductionOrder::whereIn('status', ['active', 'draft'])
            ->with('product')
            ->orderByDesc('created_at')
            ->take(50)
            ->get()
            ->map(fn($o) => [$o->order_no, $o->product->name ?? '-', $o->status])
            ->toArray();
    }

    public function headings(): array { return ['no_order', 'produk', 'status']; }
    public function title(): string { return 'Referensi Order'; }
    public function columnWidths(): array { return ['A' => 18, 'B' => 35, 'C' => 12]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

class WipStationRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return Station::where('is_active', true)->orderBy('order_sequence')->get()
            ->map(fn($s) => [$s->code, $s->name])->toArray();
    }

    public function headings(): array { return ['kode_stasiun', 'nama_stasiun']; }
    public function title(): string { return 'Referensi Stasiun'; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 30]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

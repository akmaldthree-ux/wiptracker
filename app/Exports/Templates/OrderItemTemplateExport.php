<?php
namespace App\Exports\Templates;

use App\Models\Color;
use App\Models\Size;
use App\Models\Series;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrderItemTemplateExport implements WithMultipleSheets
{
    public function __construct(private int $seriesId) {}

    public function sheets(): array
    {
        return [
            new OrderItemDataSheet($this->seriesId),
            new OrderItemColorRefSheet(),
            new OrderItemSizeRefSheet(),
        ];
    }
}

class OrderItemDataSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function __construct(private int $seriesId) {}

    public function array(): array
    {
        $colors = Color::where('is_active', true)->take(2)->get();
        $sizes  = Size::where('is_active', true)->take(3)->get();
        $rows   = [];
        foreach ($colors as $c) {
            foreach ($sizes as $s) {
                $rows[] = [$c->code, $s->name, 10];
            }
        }
        return $rows ?: [['MRH', 'M', 10], ['MRH', 'L', 12]];
    }

    public function headings(): array
    {
        return ['kode_warna', 'kode_ukuran', 'target_qty'];
    }

    public function title(): string { return 'Item Order'; }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 15, 'C' => 14];
    }

    public function styles($sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}

class OrderItemColorRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return Color::where('is_active', true)->orderBy('code')->get()
            ->map(fn($c) => [$c->code, $c->name])->toArray();
    }

    public function headings(): array { return ['kode_warna', 'nama_warna']; }
    public function title(): string { return 'Referensi Warna'; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 30]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

class OrderItemSizeRefSheet implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    public function array(): array
    {
        return Size::where('is_active', true)->orderBy('sort_order')->get()
            ->map(fn($s) => [$s->name, $s->type])->toArray();
    }

    public function headings(): array { return ['kode_ukuran', 'tipe']; }
    public function title(): string { return 'Referensi Ukuran'; }
    public function columnWidths(): array { return ['A' => 15, 'B' => 20]; }
    public function styles($sheet): array { return [1 => ['font' => ['bold' => true]]]; }
}

<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(protected $orders) {}

    public function collection()
    {
        return $this->orders->map(fn($o) => [
            $o->order_no,
            $o->product?->name ?? '-',
            $o->series?->name ?? '-',
            $o->getTotalTargetQty(),
            $o->status,
            $o->target_date ? \Carbon\Carbon::parse($o->target_date)->format('d/m/Y') : '-',
            $o->created_at->format('d/m/Y'),
        ]);
    }

    public function headings(): array
    {
        return ['No. Order', 'Produk', 'Series', 'Qty Target', 'Status', 'Target Tanggal', 'Tgl Dibuat'];
    }

    public function title(): string { return 'Laporan Produksi'; }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF00ADB5']]]];
    }
}

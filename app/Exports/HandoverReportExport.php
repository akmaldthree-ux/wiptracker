<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HandoverReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(protected $handovers) {}

    public function collection()
    {
        return $this->handovers->map(fn($h) => [
            $h->handover_no,
            $h->order?->order_no ?? '-',
            $h->fromStation?->name ?? '-',
            $h->toStation?->name ?? '-',
            $h->initiated_at?->format('d/m/Y') ?? '-',
            $h->items->sum('qty_sent'),
            $h->items->sum('qty_received'),
            $h->items->sum('qty_reject'),
            $h->status,
        ]);
    }

    public function headings(): array
    {
        return ['No. Handover', 'No. Order', 'Dari Stasiun', 'Ke Stasiun', 'Tanggal', 'Qty Kirim', 'Qty Terima', 'Qty Reject', 'Status'];
    }

    public function title(): string { return 'Laporan Handover'; }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF00ADB5']]]];
    }
}

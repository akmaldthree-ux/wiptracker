<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RejectReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(protected $rejects) {}

    public function collection()
    {
        return $this->rejects->map(fn($item) => [
            $item->handover?->confirmed_at?->format('d/m/Y') ?? '-',
            $item->handover?->handover_no ?? '-',
            $item->handover?->order?->order_no ?? '-',
            $item->handover?->toStation?->name ?? '-',
            $item->sku?->sku_code ?? '-',
            $item->qty_reject,
            $item->reject_type,
            $item->reject_notes ?? '-',
        ]);
    }

    public function headings(): array
    {
        return ['Tanggal', 'No. Handover', 'No. Order', 'Stasiun', 'SKU', 'Qty Reject', 'Tipe', 'Alasan'];
    }

    public function title(): string { return 'Laporan Reject'; }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFEF4444']]]];
    }
}

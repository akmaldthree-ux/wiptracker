<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HandoverReportExport implements FromCollection, WithHeadings {
    protected $data;
    public function __construct($data) { $this->data = $data; }
    public function collection() { return collect($this->data); }
    public function headings(): array {
        return ['No. Handover', 'No. Order', 'Dari Stasiun', 'Ke Stasiun', 'Tgl Handover', 'Total Dikirim', 'Total Diterima', 'Selisih', 'Status'];
    }
}

<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductionReportExport implements FromCollection, WithHeadings {
    protected $data;
    public function __construct($data) { $this->data = $data; }
    public function collection() { return collect($this->data); }
    public function headings(): array {
        return ['No. Order', 'Produk', 'Series', 'Target Qty', 'Progress %', 'Deadline', 'Status', 'Dibuat'];
    }
}

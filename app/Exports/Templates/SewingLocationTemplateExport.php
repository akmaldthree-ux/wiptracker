<?php
namespace App\Exports\Templates;
use App\Models\SewingLocation;
use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class SewingLocationTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths {
    public function array(): array {
        $rows = SewingLocation::orderBy('name')->get()->map(fn($l) => [
            $l->code, $l->name, $l->address ?? '', $l->description ?? '', $l->capacity ?? '',
        ])->toArray();
        return $rows ?: [
            ['SW001','Sewing Utama Bandung','Jl. Industri No. 1, Bandung','Unit produksi utama',500],
            ['SW002','Sewing Cabang Cimahi','Jl. Raya Cimahi No. 45','',300],
        ];
    }
    public function headings(): array { return ['kode','nama','alamat','deskripsi','kapasitas']; }
    public function title(): string { return 'Template Tempat Sewing'; }
    public function columnWidths(): array { return ['A'=>12,'B'=>30,'C'=>35,'D'=>30,'E'=>15]; }
    public function styles(Worksheet $sheet): array {
        return [1=>['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'4472C4']]]];
    }
}

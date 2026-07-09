<?php
namespace App\Exports\Templates;
use App\Models\Color;
use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class ColorTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths {
    public function array(): array {
        $rows = Color::orderBy('name')->get()->map(fn($c) => [
            $c->code, $c->name, $c->hex_code ?? '',
        ])->toArray();
        return $rows ?: [
            ['MRH','Merah','FF0000'],['HJU','Hijau','00FF00'],
            ['BRU','Biru','0000FF'],['PTH','Putih','FFFFFF'],['HTM','Hitam','000000'],
        ];
    }
    public function headings(): array { return ['kode','nama','hex_code']; }
    public function title(): string { return 'Template Warna'; }
    public function columnWidths(): array { return ['A'=>15,'B'=>30,'C'=>15]; }
    public function styles(Worksheet $sheet): array {
        return [1=>['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'4472C4']]]];
    }
}

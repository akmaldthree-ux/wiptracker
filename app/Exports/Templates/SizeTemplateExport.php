<?php
namespace App\Exports\Templates;
use App\Models\Size;
use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class SizeTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths {
    public function array(): array {
        $rows = Size::orderBy('sort_order')->get()->map(fn($s) => [
            $s->name, $s->type ?? 'regular', $s->sort_order,
        ])->toArray();
        return $rows ?: [
            ['S','regular',1],['M','regular',2],['L','regular',3],['XL','regular',4],['XXL','regular',5],
        ];
    }
    public function headings(): array { return ['nama','tipe','urutan']; }
    public function title(): string { return 'Template Ukuran'; }
    public function columnWidths(): array { return ['A'=>15,'B'=>20,'C'=>10]; }
    public function styles(Worksheet $sheet): array {
        return [1=>['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'4472C4']]]];
    }
}

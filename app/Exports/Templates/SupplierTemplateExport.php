<?php
namespace App\Exports\Templates;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\{FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class SupplierTemplateExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths {
    public function array(): array {
        $rows = Supplier::orderBy("name")->get()->map(fn($s) => [
            $s->code, $s->name, $s->phone ?? "", $s->email ?? "",
            $s->address ?? "", $s->contact_person ?? "", $s->notes ?? "",
        ])->toArray();
        return $rows ?: [
            ["SUP001","PT Bahan Jaya","0812-3456-7890","bahan@jaya.com","Jl. Industri No. 1, Jakarta","Budi Santoso",""],
            ["SUP002","CV Tekstil Maju","0821-9876-5432","","Jl. Garmen No. 5, Bandung","Siti Rahayu","Supplier benang"],
        ];
    }
    public function headings(): array { return ["kode","nama","telepon","email","alamat","kontak_person","catatan"]; }
    public function title(): string { return "Template Supplier"; }
    public function columnWidths(): array { return ["A"=>12,"B"=>30,"C"=>18,"D"=>25,"E"=>40,"F"=>25,"G"=>30]; }
    public function styles(Worksheet $sheet): array {
        return [1 => ["font"=>["bold"=>true,"color"=>["rgb"=>"FFFFFF"]],"fill"=>["fillType"=>"solid","startColor"=>["rgb"=>"4472C4"]]]];
    }
}
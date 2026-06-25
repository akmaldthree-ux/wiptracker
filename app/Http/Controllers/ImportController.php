<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Imports\ColorImport;
use App\Imports\SizeImport;
use App\Imports\SupplierImport;
use App\Imports\RawMaterialImport;
use App\Imports\BomImport;
use App\Imports\OrderItemImport;
use App\Imports\WipImport;
use App\Exports\Templates\ProductTemplateExport;
use App\Exports\Templates\ColorTemplateExport;
use App\Exports\Templates\SizeTemplateExport;
use App\Exports\Templates\SupplierTemplateExport;
use App\Exports\Templates\RawMaterialTemplateExport;
use App\Exports\Templates\BomTemplateExport;
use App\Exports\Templates\OrderItemTemplateExport;
use App\Exports\Templates\WipTemplateExport;
use App\Models\ProductionOrder;

class ImportController extends Controller
{
    // ── Templates ──────────────────────────────────────────────

    public function templateProduct()
    {
        return Excel::download(new ProductTemplateExport(), 'template-produk.xlsx');
    }

    public function templateColor()
    {
        return Excel::download(new ColorTemplateExport(), 'template-warna.xlsx');
    }

    public function templateSize()
    {
        return Excel::download(new SizeTemplateExport(), 'template-ukuran.xlsx');
    }

    public function templateSupplier()
    {
        return Excel::download(new SupplierTemplateExport(), 'template-supplier.xlsx');
    }

    public function templateRawMaterial()
    {
        return Excel::download(new RawMaterialTemplateExport(), 'template-bahan-baku.xlsx');
    }

    public function templateBom()
    {
        return Excel::download(new BomTemplateExport(), 'template-bom.xlsx');
    }

    public function templateOrderItem(ProductionOrder $order)
    {
        return Excel::download(new OrderItemTemplateExport($order->series_id), "template-item-order-{$order->order_no}.xlsx");
    }

    public function templateWip()
    {
        return Excel::download(new WipTemplateExport(), 'template-wip.xlsx');
    }

    // ── Imports ────────────────────────────────────────────────

    public function importProduct(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new ProductImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        return back()->with('success', 'Import produk selesai.')->with('import_errors', $errors->toArray());
    }

    public function importColor(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new ColorImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        return back()->with('success', 'Import warna selesai.')->with('import_errors', $errors->toArray());
    }

    public function importSize(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new SizeImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        return back()->with('success', 'Import ukuran selesai.')->with('import_errors', $errors->toArray());
    }

    public function importSupplier(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new SupplierImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        return back()->with('success', 'Import supplier selesai.')->with('import_errors', $errors->toArray());
    }

    public function importRawMaterial(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new RawMaterialImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        return back()->with('success', 'Import bahan baku selesai.')->with('import_errors', $errors->toArray());
    }

    public function importBom(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new BomImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        $warnings = array_merge($errors->toArray(), $import->notFound);
        return back()->with('success', "Import BOM selesai. {$import->imported} baris berhasil.")->with('import_errors', $warnings);
    }

    public function importOrderItem(Request $request, ProductionOrder $order)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new OrderItemImport($order->id, $order->series_id);
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        $warnings = array_merge($errors->toArray(), $import->notFound);
        return back()->with('success', "Import item order selesai. {$import->imported} SKU berhasil.")->with('import_errors', $warnings);
    }

    public function importWip(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);
        $import = new WipImport();
        Excel::import($import, $request->file('file'));
        $errors = collect($import->failures())->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()));
        $warnings = array_merge($errors->toArray(), $import->notFound);
        return back()->with('success', "Import WIP selesai. {$import->imported} baris berhasil.")->with('import_errors', $warnings);
    }
}

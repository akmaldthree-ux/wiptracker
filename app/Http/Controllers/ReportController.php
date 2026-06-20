<?php
namespace App\Http\Controllers;

use App\Exports\{ProductionReportExport, HandoverReportExport, RejectReportExport};
use App\Models\{ProductionOrder, WipEntry, Handover, HandoverItem, RawMaterial, Budget, Station};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index() { return view('laporan.index'); }

    public function production(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? today()->toDateString();
        $stations = Station::orderBy('order_sequence')->get();
        $data = $stations->map(function($s) use ($dateFrom,$dateTo) {
            $entries = WipEntry::where('station_id',$s->id)->whereBetween('input_date',[$dateFrom,$dateTo]);
            return ['station'=>$s,'qty_in'=>$entries->sum('qty_in'),'qty_out'=>$entries->sum('qty_out'),'qty_reject'=>$entries->sum('qty_reject')];
        });
        $orders = ProductionOrder::with(['product','series'])->when($request->product_id, fn($q,$v)=>$q->where('product_id',$v))->when($request->status, fn($q,$v)=>$q->where('status',$v))->whereBetween('created_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->get();
        $products = \App\Models\Product::where('is_active',true)->orderBy('name')->get();
        return view('laporan.produksi', compact('data','orders','dateFrom','dateTo','stations','products'));
    }

    public function exportProductionExcel(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $orders   = ProductionOrder::with(['product','series'])->whereBetween('created_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->get();
        return Excel::download(new ProductionReportExport($orders), 'laporan-produksi-'.$dateFrom.'-'.$dateTo.'.xlsx');
    }

    public function exportProductionPdf(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $orders   = ProductionOrder::with(['product','series'])->whereBetween('created_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->get();
        $pdf = Pdf::loadView('laporan.pdf.production', compact('orders','dateFrom','dateTo'))->setPaper('a4','landscape');
        return $pdf->download('laporan-produksi-'.$dateFrom.'-'.$dateTo.'.pdf');
    }

    public function handover(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order.product','fromStation','toStation','initiatedBy','items'])
            ->whereBetween('initiated_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->latest()->get();
        $discrepancyTotal = $handovers->sum(fn($h) => abs($h->items->sum('discrepancy') ?? 0));
        return view('laporan.handover', compact('handovers','dateFrom','dateTo','discrepancyTotal'));
    }

    public function exportHandoverExcel(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order','fromStation','toStation','items'])->whereBetween('initiated_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->latest()->get();
        return Excel::download(new HandoverReportExport($handovers), 'laporan-handover-'.$dateFrom.'-'.$dateTo.'.xlsx');
    }

    public function exportHandoverPdf(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order','fromStation','toStation','items'])->whereBetween('initiated_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->latest()->get();
        $pdf = Pdf::loadView('laporan.pdf.handover', compact('handovers','dateFrom','dateTo'))->setPaper('a4','landscape');
        return $pdf->download('laporan-handover-'.$dateFrom.'-'.$dateTo.'.pdf');
    }

    public function material(Request $request)
    {
        $materials = RawMaterial::with('receipts')->orderBy('category')->orderBy('name')->get();
        $totalValue = $materials->sum(fn($m) => $m->current_stock * $m->unit_price);
        $lowStock = $materials->filter(fn($m) => $m->isBelowMinStock());
        return view('laporan.bahan-baku', compact('materials','totalValue','lowStock'));
    }

    public function budget(Request $request)
    {
        $budgets = Budget::with(['order.product'])->get();
        return view('laporan.budget', compact('budgets'));
    }

    public function exportRejectExcel(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month.'-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $rejects = HandoverItem::with(['handover.order','handover.toStation','sku'])
            ->whereNotNull('reject_type')->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status',['confirmed','approved','discrepancy'])->whereBetween('confirmed_at',[$start,$end]))
            ->orderByDesc('updated_at')->get();
        return Excel::download(new RejectReportExport($rejects), 'laporan-reject-'.$month.'.xlsx');
    }

    public function exportRejectPdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month.'-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();
        $recentRejects = HandoverItem::with(['handover.order','handover.toStation','sku'])
            ->whereNotNull('reject_type')->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status',['confirmed','approved','discrepancy'])->whereBetween('confirmed_at',[$start,$end]))
            ->orderByDesc('updated_at')->get();
        $pdf = Pdf::loadView('laporan.pdf.reject', compact('recentRejects','month'))->setPaper('a4','landscape');
        return $pdf->download('laporan-reject-'.$month.'.pdf');
    }
}

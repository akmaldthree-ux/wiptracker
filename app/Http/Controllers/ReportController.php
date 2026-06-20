<?php
namespace App\Http\Controllers;
use App\Models\{ProductionOrder, WipEntry, Handover, HandoverItem, RawMaterial, Budget, Station};
use App\Exports\{ProductionReportExport, HandoverReportExport, RejectReportExport};
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

    public function handover(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order.product','fromStation','toStation','initiatedBy','items'])
            ->whereBetween('initiated_at',[$dateFrom.' 00:00:00',$dateTo.' 23:59:59'])->latest()->get();
        $discrepancyTotal = $handovers->sum(fn($h) => abs($h->items->sum('discrepancy') ?? 0));
        return view('laporan.handover', compact('handovers','dateFrom','dateTo','discrepancyTotal'));
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

    // ─── Export Production ───────────────────────────────────────────────────

    public function exportProductionExcel(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $orders   = ProductionOrder::with(['product','series'])
            ->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
            ->get()
            ->map(fn($o) => [
                $o->order_no,
                $o->product->name ?? '-',
                $o->series->name ?? '-',
                $o->getTotalTargetQty(),
                $o->getProgressPercentage().'%',
                $o->target_date?->format('d/m/Y'),
                $o->getStatusLabelAttribute(),
                $o->created_at->format('d/m/Y'),
            ]);
        return Excel::download(new ProductionReportExport($orders), 'laporan-produksi-'.now()->format('Ymd').'.xlsx');
    }

    public function exportProductionPdf(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? today()->toDateString();
        $orders   = ProductionOrder::with(['product','series'])
            ->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
            ->get();
        $pdf = Pdf::loadView('laporan.pdf.production', compact('orders','dateFrom','dateTo'));
        return $pdf->download('laporan-produksi-'.now()->format('Ymd').'.pdf');
    }

    // ─── Export Handover ─────────────────────────────────────────────────────

    public function exportHandoverExcel(Request $request)
    {
        $dateFrom  = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo    = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order','fromStation','toStation'])
            ->whereBetween('initiated_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
            ->latest()->get()
            ->map(fn($h) => [
                $h->handover_no,
                $h->order->order_no ?? '-',
                $h->fromStation->name ?? '-',
                $h->toStation->name ?? '-',
                $h->initiated_at?->format('d/m/Y'),
                $h->items->sum('qty_sent'),
                $h->items->sum('qty_received'),
                $h->items->sum('discrepancy'),
                $h->status,
            ]);
        return Excel::download(new HandoverReportExport($handovers), 'laporan-handover-'.now()->format('Ymd').'.xlsx');
    }

    public function exportHandoverPdf(Request $request)
    {
        $dateFrom  = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo    = $request->date_to ?? today()->toDateString();
        $handovers = Handover::with(['order','fromStation','toStation','items'])
            ->whereBetween('initiated_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
            ->latest()->get();
        $pdf = Pdf::loadView('laporan.pdf.handover', compact('handovers','dateFrom','dateTo'));
        return $pdf->download('laporan-handover-'.now()->format('Ymd').'.pdf');
    }

    // ─── Export Reject ───────────────────────────────────────────────────────

    public function exportRejectExcel(Request $request)
    {
        $rejectItems = HandoverItem::with(['handover.order','sku'])
            ->where('qty_reject', '>', 0)
            ->whereNotNull('reject_type')
            ->get()
            ->map(fn($i) => [
                $i->handover->handover_no ?? '-',
                $i->handover->order->order_no ?? '-',
                $i->sku->sku_code ?? '-',
                $i->qty_reject,
                $i->reject_type,
                $i->handover->confirmed_at?->format('d/m/Y'),
                $i->reject_notes,
            ]);
        return Excel::download(new RejectReportExport($rejectItems), 'laporan-reject-'.now()->format('Ymd').'.xlsx');
    }

    public function exportRejectPdf(Request $request)
    {
        $rejectItems = HandoverItem::with(['handover.order','sku'])
            ->where('qty_reject', '>', 0)
            ->whereNotNull('reject_type')
            ->get();
        $pdf = Pdf::loadView('laporan.pdf.reject', compact('rejectItems'));
        return $pdf->download('laporan-reject-'.now()->format('Ymd').'.pdf');
    }
}

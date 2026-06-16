<?php
namespace App\Http\Controllers;
use App\Models\{ProductionOrder, WipEntry, Handover, RawMaterial, Budget, Station};
use Illuminate\Http\Request;

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
}

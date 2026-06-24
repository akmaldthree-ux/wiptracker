<?php
namespace App\Http\Controllers;

use App\Models\{HandoverItem, Handover, Station, ProductionOrder, SecondStock};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RejectController extends Controller
{
    public function dashboard(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $baseQuery = HandoverItem::query()
            ->whereNotNull('reject_type')
            ->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status', ['confirmed', 'approved', 'discrepancy'])
                ->whereBetween('confirmed_at', [$start, $end]));

        $totalRejectQty = (clone $baseQuery)->sum('qty_reject');
        $totalRework    = (clone $baseQuery)->where('reject_type', 'rework')->sum('qty_reject');
        $totalSecond    = (clone $baseQuery)->where('reject_type', 'second')->sum('qty_reject');
        $totalScrap     = (clone $baseQuery)->where('reject_type', 'scrap')->sum('qty_reject');

        $totalProduced = \App\Models\WipEntry::whereBetween('input_date', [$start->toDateString(), $end->toDateString()])->sum('qty_out');
        $rejectRate    = $totalProduced > 0 ? round(($totalRejectQty / $totalProduced) * 100, 2) : 0;

        // Kerugian scrap: qty_scrap × selling_price per order
        $scrapLoss = HandoverItem::query()
            ->select('handovers.production_order_id', DB::raw('SUM(handover_items.qty_reject) as qty_scrap'))
            ->join('handovers','handovers.id','=','handover_items.handover_id')
            ->where('handover_items.reject_type','scrap')
            ->where('handover_items.qty_reject','>',0)
            ->whereIn('handovers.status',['confirmed','approved','discrepancy'])
            ->whereBetween('handovers.confirmed_at',[$start,$end])
            ->groupBy('handovers.production_order_id')
            ->get()
            ->sum(function ($row) {
                $order = ProductionOrder::find($row->production_order_id);
                return $row->qty_scrap * (float)($order?->selling_price ?? 0);
            });

        // Rework tracking: berhasil vs gagal
        $reworkCompleted = Handover::where('is_rework', true)->where('rework_result','completed')
            ->whereBetween('created_at',[$start,$end])->count();
        $reworkFailed    = Handover::where('is_rework', true)->where('rework_result','failed')
            ->whereBetween('created_at',[$start,$end])->count();
        $reworkPending   = Handover::where('is_rework', true)->where('rework_result','pending')
            ->whereBetween('created_at',[$start,$end])->count();

        // Second stock tersedia
        $secondStockAvailable = SecondStock::where('status','available')->count();
        $secondStockQty       = SecondStock::where('status','available')->sum('qty');

        $rejectByStation = HandoverItem::query()
            ->select('handovers.to_station_id', DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers','handovers.id','=','handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')->where('handover_items.qty_reject','>',0)
            ->whereIn('handovers.status',['confirmed','approved','discrepancy'])
            ->whereBetween('handovers.confirmed_at',[$start,$end])
            ->groupBy('handovers.to_station_id')->get()
            ->map(fn($row) => ['station' => Station::find($row->to_station_id)?->name ?? 'Unknown','total' => (int)$row->total])
            ->sortByDesc('total')->values();

        $rejectByType = HandoverItem::query()
            ->select('reject_type', DB::raw('SUM(qty_reject) as total'))
            ->whereNotNull('reject_type')->where('qty_reject','>',0)
            ->whereHas('handover', fn($q) => $q->whereIn('status',['confirmed','approved','discrepancy'])->whereBetween('confirmed_at',[$start,$end]))
            ->groupBy('reject_type')->pluck('total','reject_type');

        $rejectByOrder = HandoverItem::query()
            ->select('handovers.production_order_id', DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers','handovers.id','=','handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')->where('handover_items.qty_reject','>',0)
            ->whereIn('handovers.status',['confirmed','approved','discrepancy'])
            ->whereBetween('handovers.confirmed_at',[$start,$end])
            ->groupBy('handovers.production_order_id')->orderByDesc('total')->limit(10)->get()
            ->map(fn($row) => ['order_no' => ProductionOrder::find($row->production_order_id)?->order_no ?? '—','product' => ProductionOrder::with('product')->find($row->production_order_id)?->product?->name ?? '—','total' => (int)$row->total]);

        $trendReject = HandoverItem::query()
            ->select(DB::raw('DATE(handovers.confirmed_at) as date'), DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers','handovers.id','=','handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')->where('handover_items.qty_reject','>',0)
            ->whereIn('handovers.status',['confirmed','approved','discrepancy'])
            ->where('handovers.confirmed_at','>=',now()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(handovers.confirmed_at)'))->orderBy('date')->get()->pluck('total','date');

        $recentRejects = HandoverItem::with(['handover.order.product','handover.toStation','handover.confirmedBy','sku.color','sku.size'])
            ->whereNotNull('reject_type')->where('qty_reject','>',0)
            ->whereHas('handover', fn($q) => $q->whereIn('status',['confirmed','approved','discrepancy']))
            ->orderByDesc('updated_at')->take(20)->get();

        // Rework aktif (pending)
        $activeReworks = Handover::with(['order.product','fromStation','toStation','items'])
            ->where('is_rework',true)->where('rework_result','pending')
            ->whereIn('status',['pending','confirmed'])->latest()->take(10)->get();

        // Second stock list
        $secondStocks = SecondStock::with(['order.product','sku.color','sku.size','fromStation'])
            ->where('status','available')->latest()->take(15)->get();

        $months = collect(range(0,5))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('dashboard.reject', compact(
            'totalRejectQty','totalRework','totalSecond','totalScrap',
            'rejectRate','totalProduced','scrapLoss',
            'reworkCompleted','reworkFailed','reworkPending',
            'secondStockAvailable','secondStockQty',
            'rejectByStation','rejectByType','rejectByOrder',
            'trendReject','recentRejects','month','months',
            'activeReworks','secondStocks'
        ));
    }

    public function updateRework(Request $request, Handover $handover)
    {
        abort_if(!$handover->is_rework, 404);
        abort_if(!in_array(auth()->user()->role, ['admin','supervisor']), 403);
        $request->validate(['rework_result' => 'required|in:completed,failed']);
        $handover->update(['rework_result' => $request->rework_result]);
        return back()->with('success', 'Status rework diperbarui: '.$handover->rework_result_label);
    }

    public function updateSecondStock(Request $request, SecondStock $secondStock)
    {
        abort_if(!in_array(auth()->user()->role, ['admin','supervisor']), 403);
        $request->validate(['status' => 'required|in:sold,scrapped', 'discount_price' => 'nullable|numeric']);
        $secondStock->update($request->only(['status','discount_price','notes']));
        return back()->with('success', 'Status second stock diperbarui.');
    }
}

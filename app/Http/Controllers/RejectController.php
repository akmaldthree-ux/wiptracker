<?php
namespace App\Http\Controllers;

use App\Models\{HandoverItem, Handover, Station, ProductionOrder};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RejectController extends Controller
{
    public function dashboard(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        // Base query: handover items with reject > 0, joined to confirmed/approved handovers
        $baseQuery = HandoverItem::query()
            ->whereNotNull('reject_type')
            ->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status', ['confirmed', 'approved', 'discrepancy'])
                ->whereBetween('confirmed_at', [$start, $end]));

        // KPI Cards
        $totalRejectQty  = (clone $baseQuery)->sum('qty_reject');
        $totalRework     = (clone $baseQuery)->where('reject_type', 'rework')->sum('qty_reject');
        $totalSecond     = (clone $baseQuery)->where('reject_type', 'second')->sum('qty_reject');
        $totalScrap      = (clone $baseQuery)->where('reject_type', 'scrap')->sum('qty_reject');

        // Total produksi bulan ini (qty_out WIP = throughput)
        $totalProduced = \App\Models\WipEntry::whereBetween('input_date', [$start->toDateString(), $end->toDateString()])
            ->sum('qty_out');
        $rejectRate = $totalProduced > 0 ? round(($totalRejectQty / $totalProduced) * 100, 2) : 0;

        // Reject by stasiun (to_station = stasiun penerima yang nemu reject)
        $rejectByStation = HandoverItem::query()
            ->select('handovers.to_station_id', DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers', 'handovers.id', '=', 'handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')
            ->where('handover_items.qty_reject', '>', 0)
            ->whereIn('handovers.status', ['confirmed', 'approved', 'discrepancy'])
            ->whereBetween('handovers.confirmed_at', [$start, $end])
            ->groupBy('handovers.to_station_id')
            ->with('handover:id,to_station_id')
            ->get()
            ->map(function ($row) {
                $station = Station::find($row->to_station_id);
                return ['station' => $station?->name ?? 'Unknown', 'total' => (int) $row->total];
            })
            ->sortByDesc('total')->values();

        // Reject by tipe
        $rejectByType = HandoverItem::query()
            ->select('reject_type', DB::raw('SUM(qty_reject) as total'))
            ->whereNotNull('reject_type')
            ->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status', ['confirmed', 'approved', 'discrepancy'])
                ->whereBetween('confirmed_at', [$start, $end]))
            ->groupBy('reject_type')
            ->pluck('total', 'reject_type');

        // Reject by order/produk
        $rejectByOrder = HandoverItem::query()
            ->select('handovers.production_order_id', DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers', 'handovers.id', '=', 'handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')
            ->where('handover_items.qty_reject', '>', 0)
            ->whereIn('handovers.status', ['confirmed', 'approved', 'discrepancy'])
            ->whereBetween('handovers.confirmed_at', [$start, $end])
            ->groupBy('handovers.production_order_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $order = ProductionOrder::with('product')->find($row->production_order_id);
                return [
                    'order_no' => $order?->order_no ?? '—',
                    'product'  => $order?->product?->name ?? '—',
                    'total'    => (int) $row->total,
                ];
            });

        // Trend reject 30 hari terakhir (per hari)
        $trendReject = HandoverItem::query()
            ->select(DB::raw('DATE(handovers.confirmed_at) as date'), DB::raw('SUM(handover_items.qty_reject) as total'))
            ->join('handovers', 'handovers.id', '=', 'handover_items.handover_id')
            ->whereNotNull('handover_items.reject_type')
            ->where('handover_items.qty_reject', '>', 0)
            ->whereIn('handovers.status', ['confirmed', 'approved', 'discrepancy'])
            ->where('handovers.confirmed_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy(DB::raw('DATE(handovers.confirmed_at)'))
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        // Tabel reject terbaru (dengan foto)
        $recentRejects = HandoverItem::with([
                'handover.order.product',
                'handover.toStation',
                'handover.confirmedBy',
                'sku.color',
                'sku.size',
            ])
            ->whereNotNull('reject_type')
            ->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status', ['confirmed', 'approved', 'discrepancy']))
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        // Bulan-bulan untuk filter
        $months = collect(range(0, 5))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('dashboard.reject', compact(
            'totalRejectQty', 'totalRework', 'totalSecond', 'totalScrap',
            'rejectRate', 'totalProduced',
            'rejectByStation', 'rejectByType', 'rejectByOrder',
            'trendReject', 'recentRejects', 'month', 'months'
        ));
    }
}

<?php
namespace App\Http\Controllers;
use App\Models\{ProductionOrder, Station, WipEntry, Handover, RawMaterial, Budget, User, Notification, OrderStationDeadline};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function executive()
    {
        $totalActiveOrders = ProductionOrder::where('status', 'active')->count();
        $totalWipUnits = WipEntry::selectRaw('SUM(qty_in - qty_out - qty_reject) as total')->value('total') ?? 0;
        $todayThroughput = WipEntry::where('input_date', today())->sum('qty_out');
        $overdueOrders = ProductionOrder::where('status', 'active')->where('target_date', '<', today())->count();
        $pendingHandovers = Handover::where('status', 'pending')->count();
        $discrepancyHandovers = Handover::where('status', 'discrepancy')->count();
        $lowStockMaterials = RawMaterial::whereRaw('current_stock < min_stock')->count();

        $stations = Station::withCount(['wipEntries as wip_total' => fn($q) => $q->selectRaw('SUM(qty_in - qty_out - qty_reject)')])->orderBy('order_sequence')->get();
        $activeOrders = ProductionOrder::with(['product','series','items'])->where('status','active')->orderBy('target_date')->get();

        $budgets = Budget::with('order')->get();
        $totalBudgetPlan = $budgets->sum('total_plan');
        $totalBudgetActual = $budgets->sum('total_actual');

        $weeklyThroughput = WipEntry::selectRaw("input_date, SUM(qty_out) as total")
            ->where('input_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('input_date')->orderBy('input_date')->get();

        $stationWip = Station::orderBy('order_sequence')->get()->map(function($s) {
            $wip = WipEntry::where('station_id', $s->id)->selectRaw('SUM(qty_in - qty_out - qty_reject) as total')->value('total') ?? 0;
            return ['name' => $s->name, 'wip' => max(0, $wip), 'threshold' => $s->bottleneck_threshold, 'is_bottleneck' => $wip > $s->bottleneck_threshold];
        });

        $urgentDeadlines = OrderStationDeadline::with(['order.product','station'])
            ->whereHas('order', fn($q) => $q->where('status','active'))
            ->where('target_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('target_date')
            ->get();

        return view('dashboard.executive', compact(
            'totalActiveOrders','totalWipUnits','todayThroughput','overdueOrders',
            'pendingHandovers','discrepancyHandovers','lowStockMaterials',
            'stations','activeOrders','totalBudgetPlan','totalBudgetActual',
            'weeklyThroughput','stationWip','urgentDeadlines'
        ));
    }

    public function stationDeadlines(Request $request)
    {
        $query = OrderStationDeadline::with(['order.product','order.series','station'])
            ->whereHas('order', fn($q) => $q->where('status','active'))
            ->orderBy('target_date');

        if ($request->station_id) $query->where('station_id', $request->station_id);
        if ($request->status) {
            $today = today()->toDateString();
            match($request->status) {
                'overdue'  => $query->where('target_date', '<', $today),
                'critical' => $query->whereBetween('target_date', [$today, now()->addDays(2)->toDateString()]),
                'warning'  => $query->whereBetween('target_date', [now()->addDays(3)->toDateString(), now()->addDays(7)->toDateString()]),
                'ontrack'  => $query->where('target_date', '>', now()->addDays(7)->toDateString()),
                default    => null,
            };
        }

        $deadlines = $query->get();
        $stations  = Station::where('is_active', true)->orderBy('order_sequence')->get();

        return view('dashboard.station-deadlines', compact('deadlines','stations'));
    }

    public function operational()
    {
        $stations = Station::orderBy('order_sequence')->get()->map(function($s) {
            $s->qty_in_total = WipEntry::where('station_id',$s->id)->sum('qty_in');
            $s->qty_out_total = WipEntry::where('station_id',$s->id)->sum('qty_out');
            $s->qty_reject_total = WipEntry::where('station_id',$s->id)->sum('qty_reject');
            $s->qty_in_process = max(0, $s->qty_in_total - $s->qty_out_total - $s->qty_reject_total);
            $s->is_bottleneck = $s->qty_in_process > $s->bottleneck_threshold;
            return $s;
        });

        $pendingHandovers = Handover::with(['order','fromStation','toStation','initiatedBy','items'])->where('status','pending')->latest()->get();
        $discrepancyHandovers = Handover::with(['order','fromStation','toStation'])->where('status','discrepancy')->latest()->get();
        $recentWip = WipEntry::with(['order','sku','station','creator'])->latest()->take(20)->get();
        $overdueOrders = ProductionOrder::with(['product','series'])->where('status','active')->where('target_date','<',today())->get();
        $nearDeadlineOrders = ProductionOrder::with(['product'])->where('status','active')->whereBetween('target_date',[today(),now()->addDays(3)->toDateString()])->get();

        return view('dashboard.operational', compact('stations','pendingHandovers','discrepancyHandovers','recentWip','overdueOrders','nearDeadlineOrders'));
    }

    public function wipMonitor()
    {
        // Stations sorted by sequence with net WIP aggregated
        $stations = Station::orderBy('order_sequence')->get()->map(function ($s) {
            $s->net_wip = max(0, WipEntry::where('station_id', $s->id)
                ->selectRaw('SUM(qty_in - qty_out - qty_reject) as net')->value('net') ?? 0);
            return $s;
        });

        // Active orders
        $activeOrders = ProductionOrder::with(['product', 'series', 'items'])
            ->whereIn('status', ['active', 'draft'])
            ->orderBy('target_date')
            ->get();

        // WIP per station grouped by order: { station_id => Collection of { order_no, net_wip, production_order_id } }
        $wipRaw = WipEntry::selectRaw('station_id, production_order_id, SUM(qty_in - qty_out - qty_reject) as net_wip')
            ->groupBy('station_id', 'production_order_id')
            ->having('net_wip', '>', 0)
            ->with('order:id,order_no')
            ->get();

        $wipByStation = $wipRaw->groupBy('station_id')->map(function ($items) {
            return $items->map(fn($i) => (object)[
                'production_order_id' => $i->production_order_id,
                'order_no'            => $i->order->order_no ?? '—',
                'net_wip'             => (int) $i->net_wip,
            ])->sortByDesc('net_wip')->values();
        });

        // Matrix: { order_id => { station_id => net_wip } } for chart & table
        $wipByOrderStation = [];
        foreach ($wipRaw as $row) {
            $wipByOrderStation[$row->production_order_id][$row->station_id] = (int) $row->net_wip;
        }

        // Palette for charts
        $stationColors = ['#1a3c6e','#0ea5e9','#10b981','#f59e0b','#8b5cf6'];
        $orderPalette  = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#84cc16'];
        $orderColors   = $activeOrders->values()->map(fn($o, $i) => $orderPalette[$i % count($orderPalette)])->toArray();

        // Recent WIP activity
        $recentWip = WipEntry::with(['order', 'sku', 'station', 'creator'])->latest()->take(15)->get();

        return view('dashboard.wip-monitor', compact(
            'stations', 'activeOrders', 'wipByStation', 'wipByOrderStation',
            'stationColors', 'orderColors', 'recentWip'
        ));
    }
}

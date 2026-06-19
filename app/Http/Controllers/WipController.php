<?php
namespace App\Http\Controllers;
use App\Models\{ProductionOrder, WipEntry, Station, Sku, Handover};
use Illuminate\Http\Request;

class WipController extends Controller
{
    public function index(Request $request)
    {
        $stations = Station::orderBy('order_sequence')->get();
        $orders = ProductionOrder::where('status','active')->with('product')->get();
        $q = WipEntry::with(['order.product','sku.color','sku.size','station','creator'])->latest();
        if ($request->station_id) $q->where('station_id',$request->station_id);
        if ($request->order_id) $q->where('production_order_id',$request->order_id);
        if ($request->date) $q->whereDate('input_date',$request->date);
        $entries = $q->paginate(20);

        $stationSummary = $stations->map(function($s) {
            $wip = WipEntry::where('station_id',$s->id)->selectRaw('SUM(qty_in-qty_out-qty_reject) as total')->value('total') ?? 0;
            return ['station'=>$s,'wip'=>max(0,$wip),'is_bottleneck'=>$wip>$s->bottleneck_threshold];
        });
        return view('wip.index', compact('entries','stations','orders','stationSummary'));
    }

    public function create()
    {
        abort_if(auth()->user()->role !== 'admin', 403, 'Hanya Admin yang dapat input WIP manual.');
        $user = auth()->user();
        $stations = Station::where('is_active',true)->orderBy('order_sequence')->get();
        $orders = ProductionOrder::where('status','active')->with('product')->get();
        $myStation = $user->station;
        return view('wip.create', compact('stations','orders','myStation'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'admin', 403, 'Hanya Admin yang dapat input WIP manual.');
        $request->validate([
            'production_order_id'=>'required|exists:production_orders,id',
            'station_id'=>'required|exists:stations,id',
            'sku_id'=>'required|exists:skus,id',
            'qty_in'=>'required|integer|min:0',
            'qty_out'=>'required|integer|min:0',
            'qty_reject'=>'required|integer|min:0',
            'input_date'=>'required|date',
        ]);
        WipEntry::create(array_merge($request->only(['production_order_id','station_id','sku_id','qty_in','qty_out','qty_reject','input_date','notes']),['created_by'=>auth()->id()]));
        return redirect()->route('wip.index')->with('success','Data WIP berhasil disimpan.');
    }

    public function show(ProductionOrder $order)
    {
        $order->load(['product','series','items.sku']);
        $stations = Station::orderBy('order_sequence')->get();
        $entries  = WipEntry::where('production_order_id',$order->id)->with(['sku.color','sku.size','station'])->get();
        $handovers = Handover::where('production_order_id',$order->id)
            ->with(['fromStation','toStation','initiatedBy','confirmedBy','items.sku'])
            ->orderByDesc('created_at')->get();
        return view('wip.show', compact('order','stations','entries','handovers'));
    }
}

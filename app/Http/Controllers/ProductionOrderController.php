<?php
namespace App\Http\Controllers;
use App\Models\{ProductionOrder, ProductionOrderItem, Product, Series, Sku, Station, WipEntry, OrderStationDeadline, User};
use App\Mail\MaterialReviewMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductionOrder::with(['product','series','creator'])->latest();
        if ($request->status) $q->where('status', $request->status);
        if ($request->search) $q->where('order_no','like',"%{$request->search}%")
            ->orWhereHas('product', fn($qp) => $qp->where('name','like',"%{$request->search}%"));
        $orders = $q->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active',true)->get();
        $stations = Station::where('is_active',true)->orderBy('order_sequence')->get();
        return view('orders.create', compact('products','stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'=>'required|exists:products,id',
            'series_id'=>'required|exists:series,id',
            'target_date'=>'required|date|after:today',
            'skus'=>'required|array|min:1',
            'skus.*.sku_id'=>'required|exists:skus,id',
            'skus.*.target_qty'=>'required|integer|min:1',
        ]);
        $orderNo = 'ORD-' . date('Y') . '-' . str_pad(ProductionOrder::count() + 1, 3, '0', STR_PAD_LEFT);
        $order = ProductionOrder::create([
            'order_no'=>$orderNo,'product_id'=>$request->product_id,'series_id'=>$request->series_id,
            'target_date'=>$request->target_date,'status'=>'draft','selling_price'=>$request->selling_price,
            'notes'=>$request->notes,'created_by'=>auth()->id(),
        ]);
        foreach ($request->skus as $s) {
            if (!empty($s['sku_id']) && !empty($s['target_qty']))
                ProductionOrderItem::create(['production_order_id'=>$order->id,'sku_id'=>$s['sku_id'],'target_qty'=>$s['target_qty']]);
        }
        // Save station deadlines
        if ($request->station_deadlines) {
            foreach ($request->station_deadlines as $stationId => $deadline) {
                if (!empty($deadline['target_date'])) {
                    OrderStationDeadline::create([
                        'production_order_id' => $order->id,
                        'station_id'          => $stationId,
                        'target_date'         => $deadline['target_date'],
                        'notes'               => $deadline['notes'] ?? null,
                    ]);
                }
            }
        }
        return redirect()->route('orders.show', $order)->with('success', "Order {$orderNo} berhasil dibuat.");
    }

    public function show(ProductionOrder $order)
    {
        $order->load(['product','series','creator','items.sku.color','items.sku.size','handovers.fromStation','handovers.toStation','budget','cuttingPlans.bundles','stationDeadlines.station']);
        $stations = Station::orderBy('order_sequence')->get();
        $wipByStation = [];
        foreach ($stations as $st) {
            $entries = WipEntry::where('production_order_id',$order->id)->where('station_id',$st->id)->get();
            $wipByStation[$st->id] = [
                'station' => $st, 'qty_in' => $entries->sum('qty_in'),
                'qty_out' => $entries->sum('qty_out'), 'qty_reject' => $entries->sum('qty_reject'),
                'qty_in_process' => max(0, $entries->sum('qty_in') - $entries->sum('qty_out') - $entries->sum('qty_reject')),
            ];
        }
        return view('orders.show', compact('order','stations','wipByStation'));
    }

    public function edit(ProductionOrder $order)
    {
        abort_if($order->status !== 'draft', 403, 'Hanya order berstatus Draft yang dapat diedit.');
        $products = Product::where('is_active',true)->get();
        $series   = Series::where('product_id',$order->product_id)->get();
        $skus     = Sku::where('series_id',$order->series_id)->with(['color','size'])->get();
        $stations = Station::where('is_active',true)->orderBy('order_sequence')->get();
        $existingDeadlines = $order->stationDeadlines->keyBy('station_id');
        return view('orders.edit', compact('order','products','series','skus','stations','existingDeadlines'));
    }

    public function update(Request $request, ProductionOrder $order)
    {
        $request->validate(['target_date'=>'required|date','notes'=>'nullable|string']);
        $order->update(['target_date'=>$request->target_date,'notes'=>$request->notes,'selling_price'=>$request->selling_price]);
        // Update station deadlines
        if ($request->station_deadlines) {
            foreach ($request->station_deadlines as $stationId => $deadline) {
                if (!empty($deadline['target_date'])) {
                    OrderStationDeadline::updateOrCreate(
                        ['production_order_id' => $order->id, 'station_id' => $stationId],
                        ['target_date' => $deadline['target_date'], 'notes' => $deadline['notes'] ?? null]
                    );
                } else {
                    OrderStationDeadline::where('production_order_id', $order->id)->where('station_id', $stationId)->delete();
                }
            }
        }
        return redirect()->route('orders.show',$order)->with('success','Order berhasil diperbarui.');
    }

    public function updateStatus(Request $request, ProductionOrder $order)
    {
        $request->validate(['status'=>'required|in:draft,active,completed,on_hold,cancelled']);
        $wasActive = $order->status === 'active';
        $order->update(['status'=>$request->status]);

        if ($request->status === 'active' && !$wasActive) {
            $order->load('product','series');
            $recipients = User::whereIn('role',['admin','supervisor'])->whereNotNull('email')->get();
            foreach ($recipients as $user) {
                try { Mail::to($user->email)->send(new MaterialReviewMail($order, $user)); } catch (\Exception $e) {}
            }
        }

        return back()->with('success',"Status order diubah ke: {$order->status_label}");
    }

    public function destroy(ProductionOrder $order)
    {
        abort_if($order->status !== 'draft', 403);
        $order->delete();
        return redirect()->route('orders.index')->with('success','Order berhasil dihapus.');
    }

}

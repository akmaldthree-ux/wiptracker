<?php
namespace App\Http\Controllers;
use App\Models\{Handover, HandoverItem, ProductionOrder, Station, Sku, WipEntry, Notification, User};
use Illuminate\Http\Request;

class HandoverController extends Controller
{
    public function index(Request $request)
    {
        $q = Handover::with(['order.product','fromStation','toStation','initiatedBy','items'])->latest();
        if ($request->status) $q->where('status',$request->status);
        if ($request->station_id) $q->where(fn($qq) => $qq->where('from_station_id',$request->station_id)->orWhere('to_station_id',$request->station_id));
        $handovers = $q->paginate(15);
        $stations = Station::orderBy('order_sequence')->get();
        return view('handover.index', compact('handovers','stations'));
    }

    public function create()
    {
        $user = auth()->user();
        $station = $user->station;
        $orders = ProductionOrder::where('status','active')->with('product')->get();
        $stations = Station::where('is_active',true)->orderBy('order_sequence')->get();
        return view('handover.create', compact('station','orders','stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_order_id'=>'required|exists:production_orders,id',
            'from_station_id'=>'required|exists:stations,id',
            'items'=>'required|array|min:1',
            'items.*.sku_id'=>'required|exists:skus,id',
            'items.*.qty_sent'=>'required|integer|min:1',
        ]);
        $fromStation = Station::find($request->from_station_id);
        $toStation = Station::where('order_sequence',$fromStation->order_sequence+1)->where('is_active',true)->first();
        if (!$toStation) return back()->withErrors(['from_station_id'=>'Tidak ada stasiun tujuan setelah stasiun ini.']);

        $ho = Handover::create([
            'handover_no' => 'HO-' . date('Y') . '-' . str_pad(Handover::count()+1,3,'0',STR_PAD_LEFT),
            'production_order_id'=>$request->production_order_id,
            'from_station_id'=>$request->from_station_id, 'to_station_id'=>$toStation->id,
            'status'=>'pending','initiated_by'=>auth()->id(),'notes'=>$request->notes,
            'condition_notes'=>$request->condition_notes,'initiated_at'=>now(),
        ]);
        foreach ($request->items as $item) {
            if (!empty($item['sku_id']) && !empty($item['qty_sent']))
                HandoverItem::create(['handover_id'=>$ho->id,'sku_id'=>$item['sku_id'],'qty_sent'=>$item['qty_sent']]);
        }
        // Notify PIC of destination station
        $destPICs = User::where('station_id',$toStation->id)->get();
        foreach ($destPICs as $pic) {
            Notification::create(['user_id'=>$pic->id,'title'=>"Handover Masuk: {$ho->handover_no}",'message'=>"Handover dari stasiun {$fromStation->name} menunggu konfirmasi Anda.",'type'=>'warning','link'=>"/handover/{$ho->id}","is_read"=>false]);
        }
        return redirect()->route('handover.show',$ho)->with('success',"Handover {$ho->handover_no} berhasil dibuat.");
    }

    public function show(Handover $handover)
    {
        $handover->load(['order.product','fromStation','toStation','initiatedBy','confirmedBy','approvedBy','items.sku.color','items.sku.size']);
        return view('handover.show', compact('handover'));
    }

    public function confirm(Request $request, Handover $handover)
    {
        abort_if($handover->status !== 'pending', 403);
        $request->validate(['items'=>'required|array','items.*.qty_received'=>'required|integer|min:0','items.*.discrepancy_notes'=>'nullable|string']);

        $hasDiscrepancy = false;
        foreach ($request->items as $id => $data) {
            $item = HandoverItem::find($id);
            $disc = $data['qty_received'] - $item->qty_sent;
            if ($disc != 0) $hasDiscrepancy = true;
            $item->update(['qty_received'=>$data['qty_received'],'discrepancy'=>$disc,'discrepancy_notes'=>$data['discrepancy_notes'] ?? null]);
        }
        $status = $hasDiscrepancy ? 'discrepancy' : 'confirmed';
        $handover->update(['status'=>$status,'confirmed_by'=>auth()->id(),'confirmed_at'=>now()]);

        if ($hasDiscrepancy) {
            $supervisors = User::where('role','supervisor')->orWhere('role','admin')->get();
            foreach ($supervisors as $s) {
                Notification::create(['user_id'=>$s->id,'title'=>"Discrepancy: {$handover->handover_no}",'message'=>"Ada selisih pada handover {$handover->handover_no} yang memerlukan persetujuan.",'type'=>'danger','link'=>"/handover/{$handover->id}",'is_read'=>false]);
            }
            return back()->with('warning','Handover dikonfirmasi dengan discrepancy. Menunggu persetujuan supervisor.');
        }
        return back()->with('success','Handover berhasil dikonfirmasi.');
    }

    public function approve(Request $request, Handover $handover)
    {
        abort_if($handover->status !== 'discrepancy', 403);
        abort_if(!in_array(auth()->user()->role,['admin','supervisor']), 403);
        $handover->update(['status'=>'approved','approved_by'=>auth()->id()]);
        return back()->with('success','Discrepancy handover telah disetujui.');
    }

    public function destroy(Handover $handover)
    {
        abort_if($handover->status !== 'pending', 403);
        $handover->delete();
        return redirect()->route('handover.index')->with('success','Handover dibatalkan.');
    }
}

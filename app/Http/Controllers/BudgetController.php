<?php
namespace App\Http\Controllers;
use App\Models\{Budget, ProductionOrder, CostEntry, Station, BomItem, User, Notification};
use App\Mail\BudgetAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $q = ProductionOrder::with(['product','budget'])->whereIn('status',['active','completed']);
        if ($request->search) $q->where('order_no','like',"%{$request->search}%");
        $orders = $q->latest()->paginate(20)->withQueryString();
        $totalPlan = Budget::sum('total_plan');
        $totalActual = Budget::sum('total_actual');
        return view('budget.index', compact('orders','totalPlan','totalActual'));
    }

    public function show(ProductionOrder $order)
    {
        $order->load(['product','series','budget','costEntries.station']);
        $budget = $order->budget;
        $costEntries = CostEntry::where('production_order_id',$order->id)->with(['station','creator'])->latest()->get();
        $stations = Station::orderBy('order_sequence')->get();
        return view('budget.show', compact('order','budget','costEntries','stations'));
    }

    public function edit(ProductionOrder $order)
    {
        $order->load(['items','product']);
        $budget = $order->budget ?? new Budget(['production_order_id'=>$order->id]);
        $totalQty = $order->getTotalTargetQty();
        $bomItems = BomItem::with('rawMaterial')
            ->where('product_id', $order->product_id)->get();
        $bomEstimate = $bomItems->sum(fn($b) => $b->getQtyNeeded($totalQty) * $b->rawMaterial->unit_price);
        return view('budget.edit', compact('order','budget','bomItems','bomEstimate','totalQty'));
    }

    public function update(Request $request, ProductionOrder $order)
    {
        $request->validate(['material_cost_plan'=>'required|numeric|min:0','process_cost_plan'=>'required|numeric|min:0','overhead_cost_plan'=>'required|numeric|min:0']);
        $total = $request->material_cost_plan + $request->process_cost_plan + $request->overhead_cost_plan;
        Budget::updateOrCreate(
            ['production_order_id'=>$order->id],
            array_merge($request->only(['material_cost_plan','process_cost_plan','overhead_cost_plan']),['total_plan'=>$total,'created_by'=>auth()->id()])
        );
        return redirect()->route('budget.show',$order)->with('success','Budget berhasil disimpan.');
    }

    public function storeCost(Request $request, ProductionOrder $order)
    {
        $request->validate(['type'=>'required|in:material,process,overhead','description'=>'required','amount'=>'required|numeric|min:0','entry_date'=>'required|date']);
        CostEntry::create(array_merge($request->only(['type','description','amount','station_id','entry_date']),['production_order_id'=>$order->id,'budget_id'=>optional($order->budget)->id,'created_by'=>auth()->id()]));
        // Update budget actual
        if ($order->budget) {
            $field = $request->type . '_cost_actual';
            $order->budget->increment($field, $request->amount);
            $order->budget->increment('total_actual', $request->amount);

            $budget = $order->budget->fresh();
            $util   = $budget->total_plan > 0 ? ($budget->total_actual / $budget->total_plan) * 100 : 0;
            $alertType = $budget->total_actual > $budget->total_plan ? 'over' : ($util >= 80 ? 'near' : null);

            if ($alertType) {
                $budget->load('order.product');
                $admins = User::where(function($q){ $q->whereIn('role', ['admin','supervisor','ppic'])->orWhereJsonContains('roles', 'ppic')->orWhereJsonContains('roles', 'admin')->orWhereJsonContains('roles', 'supervisor'); })->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => $alertType === 'over' ? "Over Budget: {$order->order_no}" : "Budget Hampir Penuh: {$order->order_no}",
                        'message' => $alertType === 'over' ? "Realisasi melebihi budget untuk order {$order->order_no}." : "Budget order {$order->order_no} sudah " . round($util) . "%.",
                        'type'    => $alertType === 'over' ? 'danger' : 'warning',
                        'link'    => "/budget/{$order->id}",
                        'is_read' => false,
                    ]);
                    if ($admin->email) {
                        try { Mail::to($admin->email)->send(new BudgetAlertMail($budget, $admin, $alertType)); } catch (\Throwable $e) { \Log::error('BudgetAlertMail failed', ['error' => $e->getMessage()]); }
                    }
                }
            }
        }
        return back()->with('success','Biaya berhasil dicatat.');
    }
}

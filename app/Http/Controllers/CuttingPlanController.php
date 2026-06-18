<?php
namespace App\Http\Controllers;

use App\Models\{CuttingPlan, CuttingBundle, ProductionOrder, Sku};
use Illuminate\Http\Request;
use Carbon\Carbon;

class CuttingPlanController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->get('view', 'list'); // list | calendar

        // Calendar data: semua cutting plan dalam bulan yang dipilih
        $month = $request->get('month', now()->format('Y-m'));
        $monthDate = Carbon::parse($month . '-01');

        $calendarPlans = CuttingPlan::with(['order.product'])
            ->whereBetween('planned_date', [$monthDate->startOfMonth()->copy(), $monthDate->copy()->endOfMonth()])
            ->orderBy('planned_date')
            ->get()
            ->groupBy(fn($p) => $p->planned_date->format('Y-m-d'));

        // List data
        $q = CuttingPlan::with(['order.product', 'creator', 'bundles'])->latest('planned_date');
        if ($request->status)  $q->where('status', $request->status);
        if ($request->order_id) $q->where('production_order_id', $request->order_id);
        if ($request->month_filter) {
            $d = Carbon::parse($request->month_filter . '-01');
            $q->whereBetween('planned_date', [$d->startOfMonth(), $d->endOfMonth()]);
        }
        $plans = $q->paginate(15)->withQueryString();

        $orders = ProductionOrder::whereIn('status', ['active', 'draft'])->with('product')->get();

        return view('cutting.index', compact('plans', 'calendarPlans', 'view', 'monthDate', 'orders'));
    }

    public function create()
    {
        $orders = ProductionOrder::whereIn('status', ['active', 'draft'])->with('product')->get();
        $plan = new CuttingPlan();
        return view('cutting.form', compact('orders', 'plan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'planned_date'        => 'required|date',
            'planned_qty'         => 'required|integer|min:1',
            'marker_length'       => 'nullable|numeric|min:0.1',
            'fabric_width'        => 'nullable|numeric|min:1',
            'total_layers'        => 'nullable|integer|min:1',
            'efficiency'          => 'nullable|numeric|min:0|max:100',
            'shift'               => 'nullable|string|max:20',
            'bundles'             => 'nullable|array',
            'bundles.*.sku_id'    => 'required|exists:skus,id',
            'bundles.*.qty'       => 'required|integer|min:1',
        ]);

        $plan = CuttingPlan::create([
            'plan_no'              => 'CP-' . date('Y') . '-' . str_pad(CuttingPlan::count() + 1, 3, '0', STR_PAD_LEFT),
            'production_order_id'  => $request->production_order_id,
            'planned_date'         => $request->planned_date,
            'planned_qty'          => $request->planned_qty,
            'marker_length'        => $request->marker_length,
            'fabric_width'         => $request->fabric_width,
            'total_layers'         => $request->total_layers,
            'efficiency'           => $request->efficiency,
            'shift'                => $request->shift,
            'notes'                => $request->notes,
            'status'               => $request->status ?? 'draft',
            'created_by'           => auth()->id(),
        ]);

        if ($request->bundles) {
            $bundleIdx = 1;
            foreach ($request->bundles as $b) {
                if (!empty($b['sku_id']) && !empty($b['qty'])) {
                    CuttingBundle::create([
                        'bundle_no'       => $plan->plan_no . '-B' . str_pad($bundleIdx, 2, '0', STR_PAD_LEFT),
                        'cutting_plan_id' => $plan->id,
                        'sku_id'          => $b['sku_id'],
                        'qty'             => $b['qty'],
                        'status'          => 'cut',
                        'notes'           => $b['notes'] ?? null,
                    ]);
                    $bundleIdx++;
                }
            }
        }

        return redirect()->route('cutting.show', $plan)->with('success', "Cutting Plan {$plan->plan_no} berhasil dibuat.");
    }

    public function show(CuttingPlan $cutting)
    {
        $cutting->load(['order.product', 'creator', 'bundles.sku.color', 'bundles.sku.size']);
        return view('cutting.show', compact('cutting'));
    }

    public function edit(CuttingPlan $cutting)
    {
        $orders = ProductionOrder::whereIn('status', ['active', 'draft'])->with('product')->get();
        $plan = $cutting;
        $plan->load(['bundles.sku.color', 'bundles.sku.size']);
        return view('cutting.form', compact('orders', 'plan'));
    }

    public function update(Request $request, CuttingPlan $cutting)
    {
        $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'planned_date'        => 'required|date',
            'planned_qty'         => 'required|integer|min:1',
            'actual_qty'          => 'nullable|integer|min:0',
            'marker_length'       => 'nullable|numeric|min:0.1',
            'fabric_width'        => 'nullable|numeric|min:1',
            'total_layers'        => 'nullable|integer|min:1',
            'efficiency'          => 'nullable|numeric|min:0|max:100',
        ]);

        $cutting->update($request->only([
            'production_order_id', 'planned_date', 'planned_qty', 'actual_qty',
            'marker_length', 'fabric_width', 'total_layers', 'efficiency',
            'shift', 'notes', 'status',
        ]));

        return redirect()->route('cutting.show', $cutting)->with('success', 'Cutting Plan berhasil diperbarui.');
    }

    public function destroy(CuttingPlan $cutting)
    {
        abort_if(in_array($cutting->status, ['in_progress', 'completed']), 403, 'Cutting plan yang sedang berjalan atau selesai tidak dapat dihapus.');
        $cutting->delete();
        return redirect()->route('cutting.index')->with('success', 'Cutting Plan berhasil dihapus.');
    }

    public function updateStatus(Request $request, CuttingPlan $cutting)
    {
        $request->validate(['status' => 'required|in:draft,scheduled,in_progress,completed,cancelled']);
        $data = ['status' => $request->status];
        if ($request->status === 'completed' && $request->actual_qty) {
            $data['actual_qty'] = $request->actual_qty;
        }
        $cutting->update($data);
        return back()->with('success', "Status diubah menjadi: {$cutting->fresh()->status_label}");
    }

    public function storeBundle(Request $request, CuttingPlan $cutting)
    {
        $request->validate([
            'sku_id' => 'required|exists:skus,id',
            'qty'    => 'required|integer|min:1',
            'notes'  => 'nullable|string',
        ]);

        $count = $cutting->bundles()->count() + 1;
        CuttingBundle::create([
            'bundle_no'       => $cutting->plan_no . '-B' . str_pad($count, 2, '0', STR_PAD_LEFT),
            'cutting_plan_id' => $cutting->id,
            'sku_id'          => $request->sku_id,
            'qty'             => $request->qty,
            'status'          => 'cut',
            'notes'           => $request->notes,
        ]);

        return back()->with('success', 'Bundle berhasil ditambahkan.');
    }

    public function updateBundle(Request $request, CuttingBundle $bundle)
    {
        $request->validate(['status' => 'required|in:cut,bundled,sent_to_sewing']);
        $bundle->update(['status' => $request->status]);
        return back()->with('success', "Status bundle {$bundle->bundle_no} diperbarui.");
    }
}

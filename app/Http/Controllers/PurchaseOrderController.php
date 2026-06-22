<?php
namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\MaterialReceipt;
use App\Models\Notification;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Mail\PurchaseOrderSentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = PurchaseOrder::with(['supplier','creator','items'])->latest();
        if ($request->status)      $q->where('status', $request->status);
        if ($request->supplier_id) $q->where('supplier_id', $request->supplier_id);
        $pos       = $q->paginate(15);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('purchase-order.index', compact('pos', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $materials = RawMaterial::where('is_active', true)->orderBy('name')->get();
        return view('purchase-order.create', compact('suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'            => 'required|exists:suppliers,id',
            'order_date'             => 'required|date',
            'expected_date'          => 'nullable|date|after_or_equal:order_date',
            'notes'                  => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.raw_material_id'=> 'required|exists:raw_materials,id',
            'items.*.qty_ordered'    => 'required|numeric|min:0.01',
            'items.*.unit_price'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $poNo = 'PO-' . date('Y') . '-' . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT);
            $po = PurchaseOrder::create([
                'po_no'         => $poNo,
                'supplier_id'   => $request->supplier_id,
                'status'        => 'draft',
                'order_date'    => $request->order_date,
                'expected_date' => $request->expected_date,
                'notes'         => $request->notes,
                'created_by'    => auth()->id(),
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                if (empty($item['raw_material_id']) || empty($item['qty_ordered'])) continue;
                $lineTotal = $item['qty_ordered'] * $item['unit_price'];
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'raw_material_id'   => $item['raw_material_id'],
                    'qty_ordered'       => $item['qty_ordered'],
                    'qty_received'      => 0,
                    'unit_price'        => $item['unit_price'],
                    'total_price'       => $lineTotal,
                    'notes'             => $item['notes'] ?? null,
                ]);
                $total += $lineTotal;
            }
            $po->update(['total_amount' => $total]);
        });

        return redirect()->route('purchase-order.index')->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier','creator','items.rawMaterial']);
        return view('purchase-order.show', compact('purchaseOrder'));
    }

    public function send(PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->status !== 'draft', 403);
        $purchaseOrder->load(['supplier','items.rawMaterial']);
        $purchaseOrder->update(['status' => 'sent', 'sent_at' => now()]);

        // Email ke supplier
        if ($purchaseOrder->supplier->email) {
            try { Mail::to($purchaseOrder->supplier->email)->send(new PurchaseOrderSentMail($purchaseOrder)); } catch (\Throwable $e) { \Log::error('PurchaseOrderSentMail failed', ['error' => $e->getMessage()]); }
        }

        // WhatsApp notification to supplier contact if available
        app(WhatsAppService::class)->send(
            $purchaseOrder->supplier->phone ?? '',
            "Halo {$purchaseOrder->supplier->contact_person},\n\nKami telah mengirimkan Purchase Order *{$purchaseOrder->po_no}* dari DTHREE.\n\nMohon konfirmasi penerimaan PO ini dan pastikan pengiriman sebelum " . ($purchaseOrder->expected_date?->format('d M Y') ?? '-') . ".\n\nTerima kasih."
        );

        // Notify admin
        User::where('role', 'admin')->get()->each(fn($u) => Notification::create([
            'user_id' => $u->id, 'title' => "PO Terkirim: {$purchaseOrder->po_no}",
            'message' => "Purchase Order {$purchaseOrder->po_no} ke {$purchaseOrder->supplier->name} telah dikirim.",
            'type' => 'info', 'link' => "/purchase-order/{$purchaseOrder->id}", 'is_read' => false,
        ]));

        return back()->with('success', 'PO berhasil dikirim ke supplier.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_if(!in_array($purchaseOrder->status, ['sent','partial']), 403);
        $request->validate(['items' => 'required|array']);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $allReceived = true;
            foreach ($request->items as $itemId => $data) {
                $item = PurchaseOrderItem::findOrFail($itemId);
                $qtyReceive = (float) ($data['qty_receive'] ?? 0);
                if ($qtyReceive <= 0) { $allReceived = false; continue; }

                $item->increment('qty_received', $qtyReceive);
                if ($item->fresh()->qty_received < $item->qty_ordered) $allReceived = false;

                // Update raw material stock
                $item->rawMaterial->increment('current_stock', $qtyReceive);

                // Create material receipt record
                MaterialReceipt::create([
                    'raw_material_id' => $item->raw_material_id,
                    'supplier_id'     => $purchaseOrder->supplier_id,
                    'qty'             => $qtyReceive,
                    'unit_price'      => $item->unit_price,
                    'total_price'     => $qtyReceive * $item->unit_price,
                    'supplier'        => $purchaseOrder->supplier->name,
                    'po_no'           => $purchaseOrder->po_no,
                    'receipt_date'    => now()->toDateString(),
                    'confirmed_by'    => auth()->id(),
                    'notes'           => "Penerimaan dari PO {$purchaseOrder->po_no}",
                ]);
            }

            $status = $allReceived ? 'received' : 'partial';
            $purchaseOrder->update([
                'status'      => $status,
                'received_at' => $status === 'received' ? now() : null,
            ]);

            // Notify admin
            User::where('role', 'admin')->get()->each(fn($u) => Notification::create([
                'user_id' => $u->id,
                'title'   => ($status === 'received' ? '✅ PO Diterima Lengkap: ' : '📦 PO Diterima Sebagian: ') . $purchaseOrder->po_no,
                'message' => "Penerimaan dari {$purchaseOrder->supplier->name} — stok bahan baku telah diperbarui.",
                'type'    => $status === 'received' ? 'success' : 'warning',
                'link'    => "/purchase-order/{$purchaseOrder->id}", 'is_read' => false,
            ]));
        });

        return back()->with('success', 'Penerimaan barang berhasil dicatat. Stok bahan baku diperbarui otomatis.');
    }

    public function cancel(PurchaseOrder $purchaseOrder)
    {
        abort_if(!in_array($purchaseOrder->status, ['draft','sent']), 403);
        $purchaseOrder->update(['status' => 'cancelled']);
        return back()->with('success', 'Purchase Order dibatalkan.');
    }
}

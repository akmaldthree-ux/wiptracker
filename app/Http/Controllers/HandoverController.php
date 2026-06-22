<?php
namespace App\Http\Controllers;
use App\Models\{Handover, HandoverItem, ProductionOrder, ProductionOrderItem, Station, Sku, WipEntry, Notification, User, SewingLocation, CuttingPlan, CuttingBundle};
use App\Services\WhatsAppService;
use App\Mail\HandoverCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $sewingLocations = SewingLocation::where('is_active',true)->orderBy('name')->get();
        return view('handover.create', compact('station','orders','stations','sewingLocations'));
    }

    public function store(Request $request)
    {
        $fromStation = Station::find($request->from_station_id);
        $toStation = $fromStation ? Station::where('order_sequence',$fromStation->order_sequence+1)->where('is_active',true)->first() : null;

        $requireSewingLocation = $toStation && $toStation->order_sequence == 2;

        $request->validate([
            'production_order_id'=>'required|exists:production_orders,id',
            'from_station_id'=>'required|exists:stations,id',
            'sewing_location_id'=> $requireSewingLocation ? 'required|exists:sewing_locations,id' : 'nullable|exists:sewing_locations,id',
            'items'=>'required|array|min:1',
            'items.*.sku_id'=>'required|exists:skus,id',
            'items.*.qty_sent'=>'required|integer|min:1',
            'photo_sent'=>'required|image|max:10240',
        ], [
            'sewing_location_id.required' => 'Tempat Sewing wajib dipilih saat handover ke stasiun Sewing.',
            'photo_sent.required' => 'Foto bukti pengiriman wajib disertakan.',
            'photo_sent.image'   => 'File harus berupa gambar (jpg, png, webp).',
            'photo_sent.max'     => 'Ukuran foto maksimal 10MB.',
        ]);

        if (!$toStation) return back()->withErrors(['from_station_id'=>'Tidak ada stasiun tujuan setelah stasiun ini.']);

        $wipEntries = WipEntry::where('production_order_id', $request->production_order_id)
            ->where('station_id', $request->from_station_id)
            ->selectRaw('sku_id, SUM(qty_in) as total_in, SUM(qty_out) as total_out, SUM(qty_reject) as total_reject')
            ->groupBy('sku_id')->get()->keyBy('sku_id');

        foreach ($request->items as $item) {
            if (empty($item['sku_id']) || empty($item['qty_sent'])) continue;
            $e = $wipEntries->get($item['sku_id']);
            $available = $e ? max(0, $e->total_in - $e->total_out - $e->total_reject) : 0;
            if ((int)$item['qty_sent'] > $available) {
                $sku = \App\Models\Sku::find($item['sku_id']);
                $skuCode = $sku ? $sku->sku_code : "SKU #{$item['sku_id']}";
                return back()->withInput()->withErrors(['items' => "SKU {$skuCode}: qty yang dikirim ({$item['qty_sent']}) melebihi stok tersedia di stasiun ({$available} pcs)."]);
            }
        }

        $photoSentPath = $this->compressAndStore($request->file('photo_sent'), 'handovers/sent');

        $ho = Handover::create([
            'handover_no'          => 'HO-' . date('Y') . '-' . str_pad(Handover::count()+1,3,'0',STR_PAD_LEFT),
            'production_order_id'  => $request->production_order_id,
            'from_station_id'      => $request->from_station_id,
            'to_station_id'        => $toStation->id,
            'sewing_location_id'   => $request->sewing_location_id ?: null,
            'status'               => 'pending',
            'initiated_by'         => auth()->id(),
            'notes'                => $request->notes,
            'condition_notes'      => $request->condition_notes,
            'photo_sent'           => $photoSentPath,
            'initiated_at'         => now(),
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['sku_id']) && !empty($item['qty_sent']))
                HandoverItem::create(['handover_id'=>$ho->id,'sku_id'=>$item['sku_id'],'qty_sent'=>$item['qty_sent']]);
        }

        $wa = app(WhatsAppService::class);
        $waMsg = "Handover Masuk: *{$ho->handover_no}*\nDari: {$fromStation->name} → {$toStation->name}\nOrder: {$ho->order->order_no}\nKonfirmasi di: " . url("/handover/{$ho->id}");

        $destPICs = User::where('station_id',$toStation->id)->whereNotNull('email')->get();
        \Log::info('Handover email: dest PICs found', ['count' => $destPICs->count(), 'emails' => $destPICs->pluck('email')]);
        foreach ($destPICs as $pic) {
            Notification::create(['user_id'=>$pic->id,'title'=>"Handover Masuk: {$ho->handover_no}",'message'=>"Handover dari stasiun {$fromStation->name} menunggu konfirmasi Anda.",'type'=>'warning','link'=>"/handover/{$ho->id}","is_read"=>false]);
            $wa->sendToUser($pic, $waMsg);
            try {
                Mail::to($pic->email)->send(new HandoverCreatedMail($ho, $pic));
                \Log::info('Handover email sent', ['to' => $pic->email, 'handover' => $ho->handover_no]);
            } catch (\Throwable $e) {
                \Log::error('HandoverCreatedMail failed', ['to' => $pic->email, 'error' => $e->getMessage()]);
            }
        }

        // Notifikasi ke admin & supervisor
        $admins = User::whereIn('role', ['admin', 'supervisor'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => "Handover Baru: {$ho->handover_no}",
                'message' => "Handover dari {$fromStation->name} → {$toStation->name} telah dikirim oleh " . auth()->user()->name . ".",
                'type'    => 'info',
                'link'    => "/handover/{$ho->id}",
                'is_read' => false,
            ]);
        }

        return redirect()->route('handover.show',$ho)->with('success',"Handover {$ho->handover_no} berhasil dibuat.");
    }

    public function show(Handover $handover)
    {
        $handover->load(['order.product','fromStation','toStation','sewingLocation','initiatedBy','confirmedBy','approvedBy','items.sku.color','items.sku.size']);
        return view('handover.show', compact('handover'));
    }

    public function confirm(Request $request, Handover $handover)
    {
        abort_if($handover->status !== 'pending', 403);

        $request->validate([
            'items'                      => 'required|array',
            'items.*.qty_received'       => 'required|integer|min:0',
            'items.*.qty_reject'         => 'nullable|integer|min:0',
            'items.*.reject_type'        => 'nullable|in:rework,second,scrap',
            'items.*.discrepancy_notes'  => 'nullable|string',
            'items.*.reject_notes'       => 'nullable|string',
            'photo_received'             => 'required|image|max:10240',
        ], [
            'photo_received.required' => 'Foto bukti penerimaan wajib disertakan.',
            'photo_received.image'    => 'File harus berupa gambar (jpg, png, webp).',
            'photo_received.max'      => 'Ukuran foto maksimal 10MB.',
        ]);

        // Validate photo_reject & reject_type required per item when qty_reject > 0
        foreach ($request->items as $id => $data) {
            $qtyReject = (int) ($data['qty_reject'] ?? 0);
            if ($qtyReject > 0) {
                $item = HandoverItem::with('sku')->find($id);
                if (!$request->hasFile("photo_reject_{$id}")) {
                    return back()->withErrors(['photo_reject' => "Foto bukti cacat wajib disertakan untuk SKU {$item->sku->sku_code}."]);
                }
                if (empty($data['reject_type'])) {
                    return back()->withErrors(['reject_type' => "Tipe reject wajib dipilih untuk SKU {$item->sku->sku_code} (rework/second/scrap)."]);
                }
            }
        }

        $photoReceivedPath = $this->compressAndStore($request->file('photo_received'), 'handovers/received');

        $hasDiscrepancy  = false;
        $reworkItems     = []; // collect items with reject_type=rework for auto rework handover
        foreach ($request->items as $id => $data) {
            $item        = HandoverItem::find($id);
            $qtyReceived = (int) $data['qty_received'];
            $qtyReject   = (int) ($data['qty_reject'] ?? 0);
            $rejectType  = $qtyReject > 0 ? ($data['reject_type'] ?? null) : null;
            $disc        = ($qtyReceived + $qtyReject) - $item->qty_sent;
            if ($disc != 0) $hasDiscrepancy = true;

            $photoRejectPath = null;
            if ($qtyReject > 0 && $request->hasFile("photo_reject_{$id}")) {
                $photoRejectPath = $this->compressAndStore($request->file("photo_reject_{$id}"), 'handovers/reject');
            }

            $item->update([
                'qty_received'      => $qtyReceived,
                'qty_reject'        => $qtyReject,
                'reject_type'       => $rejectType,
                'reject_notes'      => $data['reject_notes'] ?? null,
                'photo_reject'      => $photoRejectPath,
                'discrepancy'       => $disc,
                'discrepancy_notes' => $data['discrepancy_notes'] ?? null,
            ]);

            if ($rejectType === 'rework' && $qtyReject > 0) {
                $reworkItems[] = ['sku_id' => $item->sku_id, 'qty' => $qtyReject];
            }
        }

        $status = $hasDiscrepancy ? 'discrepancy' : 'confirmed';
        $handover->update([
            'status'          => $status,
            'confirmed_by'    => auth()->id(),
            'confirmed_at'    => now(),
            'photo_received'  => $photoReceivedPath,
        ]);

        if (!$hasDiscrepancy) {
            $this->createWipFromHandover($handover, useReceived: true);
        }

        // Auto-create rework handover: kirim balik ke stasiun asal
        if (!empty($reworkItems) && $handover->from_station_id) {
            $reworkHo = Handover::create([
                'handover_no'         => 'HO-RW-' . date('Y') . '-' . str_pad(Handover::count() + 1, 3, '0', STR_PAD_LEFT),
                'production_order_id' => $handover->production_order_id,
                'from_station_id'     => $handover->to_station_id,
                'to_station_id'       => $handover->from_station_id,
                'status'              => 'pending',
                'initiated_by'        => auth()->id(),
                'notes'               => "Rework dari {$handover->handover_no}",
                'initiated_at'        => now(),
            ]);
            foreach ($reworkItems as $ri) {
                HandoverItem::create(['handover_id' => $reworkHo->id, 'sku_id' => $ri['sku_id'], 'qty_sent' => $ri['qty']]);
            }
            $senderPICs = User::where('station_id', $handover->from_station_id)->get();
            foreach ($senderPICs as $pic) {
                Notification::create(['user_id' => $pic->id, 'title' => "Rework Masuk: {$reworkHo->handover_no}", 'message' => "Ada barang rework dari {$handover->toStation->name} yang perlu diperbaiki.", 'type' => 'warning', 'link' => "/handover/{$reworkHo->id}", 'is_read' => false]);
            }
        }

        // Notifikasi ke pengirim: handover sudah dikonfirmasi
        $senderUser = $handover->initiatedBy;
        if ($senderUser) {
            Notification::create([
                'user_id' => $senderUser->id,
                'title'   => "Handover Dikonfirmasi: {$handover->handover_no}",
                'message' => "Handover Anda ke stasiun {$handover->toStation?->name} telah dikonfirmasi oleh " . auth()->user()->name . ".",
                'type'    => 'success',
                'link'    => "/handover/{$handover->id}",
                'is_read' => false,
            ]);
        }
        // Notifikasi ke PIC stasiun pengirim juga
        if ($handover->from_station_id) {
            $senderPICs = User::where('station_id', $handover->from_station_id)->where('id', '!=', $senderUser?->id ?? 0)->get();
            foreach ($senderPICs as $pic) {
                Notification::create([
                    'user_id' => $pic->id,
                    'title'   => "Handover Dikonfirmasi: {$handover->handover_no}",
                    'message' => "Handover dari stasiun {$handover->fromStation?->name} ke {$handover->toStation?->name} telah dikonfirmasi.",
                    'type'    => 'success',
                    'link'    => "/handover/{$handover->id}",
                    'is_read' => false,
                ]);
            }
        }
        // Notifikasi ke admin & supervisor
        $confirmMsg = $hasDiscrepancy
            ? "Handover {$handover->handover_no} dikonfirmasi dengan DISCREPANCY oleh " . auth()->user()->name . "."
            : "Handover {$handover->handover_no} ({$handover->fromStation?->name} → {$handover->toStation?->name}) telah dikonfirmasi oleh " . auth()->user()->name . ".";
        $admins = User::whereIn('role', ['admin', 'supervisor'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => ($hasDiscrepancy ? "⚠ Discrepancy" : "✓ Handover Dikonfirmasi") . ": {$handover->handover_no}",
                'message' => $confirmMsg,
                'type'    => $hasDiscrepancy ? 'danger' : 'success',
                'link'    => "/handover/{$handover->id}",
                'is_read' => false,
            ]);
        }

        if ($hasDiscrepancy) {
            $supervisors = User::where('role','supervisor')->orWhere('role','admin')->get();
            foreach ($supervisors as $s) {
                Notification::create(['user_id'=>$s->id,'title'=>"Discrepancy: {$handover->handover_no}",'message'=>"Ada selisih pada handover {$handover->handover_no} yang memerlukan persetujuan.",'type'=>'danger','link'=>"/handover/{$handover->id}",'is_read'=>false]);
            }
            return back()->with('warning','Handover dikonfirmasi dengan discrepancy. Menunggu persetujuan supervisor.');
        }

        $msg = 'Handover berhasil dikonfirmasi. WIP diperbarui otomatis.';
        if (!empty($reworkItems)) $msg .= ' Handover rework otomatis dibuat.';
        return back()->with('success', $msg);
    }

    public function approve(Request $request, Handover $handover)
    {
        abort_if($handover->status !== 'discrepancy', 403);
        abort_if(!in_array(auth()->user()->role,['admin','supervisor']), 403);
        $handover->update(['status'=>'approved','approved_by'=>auth()->id()]);

        // Auto-create WIP entries when discrepancy is approved (use qty_received as actuals)
        $this->createWipFromHandover($handover, useReceived: true);

        // Notifikasi ke pengirim dan penerima bahwa discrepancy sudah disetujui
        $notifyUsers = collect();
        if ($handover->initiatedBy) $notifyUsers->push($handover->initiatedBy);
        if ($handover->confirmedBy) $notifyUsers->push($handover->confirmedBy);
        if ($handover->from_station_id) {
            User::where('station_id', $handover->from_station_id)->get()->each(fn($u) => $notifyUsers->push($u));
        }
        if ($handover->to_station_id) {
            User::where('station_id', $handover->to_station_id)->get()->each(fn($u) => $notifyUsers->push($u));
        }
        foreach ($notifyUsers->unique('id') as $u) {
            Notification::create([
                'user_id' => $u->id,
                'title'   => "Handover Disetujui: {$handover->handover_no}",
                'message' => "Discrepancy pada handover {$handover->handover_no} telah disetujui oleh " . auth()->user()->name . ". WIP sudah diperbarui.",
                'type'    => 'success',
                'link'    => "/handover/{$handover->id}",
                'is_read' => false,
            ]);
        }

        return back()->with('success','Discrepancy handover telah disetujui. WIP diperbarui berdasarkan qty aktual yang diterima.');
    }

    public function completeOrder(Request $request, Handover $handover)
    {
        $order = $handover->order;
        abort_if(!$handover->toStation?->is_final, 403, 'Hanya stasiun akhir yang dapat menyelesaikan order.');
        abort_if($order->status === 'completed', 409, 'Order sudah diselesaikan.');
        abort_if(!in_array($handover->status, ['confirmed', 'approved']), 403, 'Handover harus sudah dikonfirmasi.');

        $canComplete = in_array(auth()->user()->role, ['admin', 'supervisor'])
            || auth()->user()->station_id == $handover->to_station_id;
        abort_if(!$canComplete, 403);

        $order->update(['status' => 'completed']);

        // WIP flush: record qty_out for the final station to close out remaining WIP
        $today = now()->toDateString();
        foreach ($handover->items as $item) {
            $qty = (int) ($item->qty_received ?? $item->qty_sent);
            if ($qty <= 0) continue;
            WipEntry::create([
                'production_order_id' => $order->id,
                'sku_id'              => $item->sku_id,
                'station_id'          => $handover->to_station_id,
                'qty_in'              => 0,
                'qty_out'             => $qty,
                'qty_reject'          => 0,
                'input_date'          => $today,
                'notes'               => "Selesai — Order {$order->order_no} ditutup oleh " . auth()->user()->name,
                'created_by'          => auth()->id(),
            ]);
        }

        // Notify all relevant parties
        $notifyUsers = collect();
        User::whereIn('role', ['admin', 'supervisor'])->get()->each(fn($u) => $notifyUsers->push($u));
        if ($handover->initiatedBy) $notifyUsers->push($handover->initiatedBy);
        if ($handover->confirmedBy) $notifyUsers->push($handover->confirmedBy);
        User::where('station_id', $handover->to_station_id)->get()->each(fn($u) => $notifyUsers->push($u));

        foreach ($notifyUsers->unique('id') as $u) {
            Notification::create([
                'user_id' => $u->id,
                'title'   => "Order Selesai: {$order->order_no}",
                'message' => "Order produksi {$order->order_no} telah diselesaikan oleh " . auth()->user()->name . " di stasiun {$handover->toStation->name}. WIP telah ditutup.",
                'type'    => 'success',
                'link'    => "/orders/{$order->id}",
                'is_read' => false,
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', "Order {$order->order_no} berhasil diselesaikan. Semua WIP di stasiun {$handover->toStation->name} telah ditutup.");
    }

    public function sendFromOrder(Request $request, ProductionOrder $order)
    {
        abort_if(!in_array(auth()->user()->role, ['admin', 'supervisor']), 403);
        $toStation = Station::where('order_sequence', 1)->where('is_active', true)->first();
        if (!$toStation) return back()->withErrors(['error' => 'Stasiun Cutting tidak ditemukan.']);

        $ho = Handover::create([
            'handover_no'          => 'HO-' . date('Y') . '-' . str_pad(Handover::count() + 1, 3, '0', STR_PAD_LEFT),
            'production_order_id'  => $order->id,
            'from_station_id'      => null,
            'to_station_id'        => $toStation->id,
            'status'               => 'pending',
            'initiated_by'         => auth()->id(),
            'notes'                => $request->notes,
            'initiated_at'         => now(),
        ]);

        foreach ($order->items as $item) {
            HandoverItem::create([
                'handover_id' => $ho->id,
                'sku_id'      => $item->sku_id,
                'qty_sent'    => $item->target_qty,
            ]);
        }

        $destPICs = User::where('station_id', $toStation->id)->get();
        foreach ($destPICs as $pic) {
            Notification::create([
                'user_id' => $pic->id,
                'title'   => "Handover Masuk dari Order: {$ho->handover_no}",
                'message' => "Order produksi {$order->order_no} dikirim ke stasiun {$toStation->name}.",
                'type'    => 'warning',
                'link'    => "/handover/{$ho->id}",
                'is_read' => false,
            ]);
        }

        // Auto-create draft Cutting Plan
        $cp = CuttingPlan::create([
            'plan_no'             => 'CP-' . date('Y') . '-' . str_pad(CuttingPlan::count() + 1, 3, '0', STR_PAD_LEFT),
            'production_order_id' => $order->id,
            'planned_date'        => now()->toDateString(),
            'planned_qty'         => $order->getTotalTargetQty(),
            'status'              => 'draft',
            'notes'               => "Auto-dibuat dari Order {$order->order_no}",
            'created_by'          => auth()->id(),
        ]);
        $bundleIdx = 1;
        foreach ($order->items as $item) {
            CuttingBundle::create([
                'bundle_no'       => $cp->plan_no . '-B' . str_pad($bundleIdx, 2, '0', STR_PAD_LEFT),
                'cutting_plan_id' => $cp->id,
                'sku_id'          => $item->sku_id,
                'qty'             => $item->target_qty,
                'status'          => 'cut',
            ]);
            $bundleIdx++;
        }

        return redirect()->route('handover.show', $ho)->with('success', "Handover {$ho->handover_no} dan Cutting Plan {$cp->plan_no} berhasil dibuat.");
    }

    /**
     * Create WIP entries from a confirmed/approved handover.
     * If from_station_id is null (PO→Cutting), only create qty_in for receiver.
     */
    private function createWipFromHandover(Handover $handover, bool $useReceived = true): void
    {
        $handover->load('items');
        $today = now()->toDateString();
        $note  = "Auto dari Handover {$handover->handover_no}";

        foreach ($handover->items as $item) {
            $qtyReceived = $useReceived ? max(0, (int) $item->qty_received) : max(0, (int) $item->qty_sent);
            $qtyReject   = $useReceived ? max(0, (int) $item->qty_reject)   : 0;
            $qtyOut      = $qtyReceived + $qtyReject;
            if ($qtyOut === 0) continue;

            // qty_out for sender station (skip if from PO, no sender station)
            if ($handover->from_station_id) {
                WipEntry::create([
                    'production_order_id' => $handover->production_order_id,
                    'sku_id'              => $item->sku_id,
                    'station_id'          => $handover->from_station_id,
                    'qty_in'              => 0,
                    'qty_out'             => $qtyOut,
                    'qty_reject'          => 0,
                    'input_date'          => $today,
                    'notes'               => $note,
                    'created_by'          => auth()->id(),
                ]);
            }

            // qty_in for receiver station
            if ($qtyReceived > 0) {
                WipEntry::create([
                    'production_order_id' => $handover->production_order_id,
                    'sku_id'              => $item->sku_id,
                    'station_id'          => $handover->to_station_id,
                    'qty_in'              => $qtyReceived,
                    'qty_out'             => 0,
                    'qty_reject'          => 0,
                    'input_date'          => $today,
                    'notes'               => $note,
                    'created_by'          => auth()->id(),
                ]);
            }

            // qty_reject recorded at receiver station
            if ($qtyReject > 0) {
                $rejectNote = $note . ($item->reject_notes ? " — Alasan reject: {$item->reject_notes}" : '');
                WipEntry::create([
                    'production_order_id' => $handover->production_order_id,
                    'sku_id'              => $item->sku_id,
                    'station_id'          => $handover->to_station_id,
                    'qty_in'              => 0,
                    'qty_out'             => 0,
                    'qty_reject'          => $qtyReject,
                    'input_date'          => $today,
                    'notes'               => $rejectNote,
                    'created_by'          => auth()->id(),
                ]);
            }
        }
    }

    /**
     * Compress and store uploaded photo using GD. Max 1920px wide, 80% JPEG quality.
     */
    private function compressAndStore(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $filename = uniqid() . '_' . time() . '.jpg';
        $destDir  = storage_path("app/public/{$folder}");
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $destPath = "{$destDir}/{$filename}";

        $mime = $file->getMimeType();
        $src  = match(true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $maxW = 1920;

        if ($srcW > $maxW) {
            $ratio  = $maxW / $srcW;
            $newW   = $maxW;
            $newH   = (int) round($srcH * $ratio);
            $canvas = imagecreatetruecolor($newW, $newH);
            // Preserve transparency for PNG
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
            imagedestroy($src);
            $src = $canvas;
        }

        imagejpeg($src, $destPath, 80);
        imagedestroy($src);

        return "storage/{$folder}/{$filename}";
    }

    public function destroy(Handover $handover)
    {
        abort_if($handover->status !== 'pending', 403);
        $handover->delete();
        return redirect()->route('handover.index')->with('success','Handover dibatalkan.');
    }
}

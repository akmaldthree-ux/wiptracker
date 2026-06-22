<?php
namespace App\Http\Controllers;
use App\Models\{QcInspection, QcChecklistItem, ProductionOrder, Handover, Notification, User};
use Illuminate\Http\Request;

class QcInspectionController extends Controller
{
    private const CHECKLIST = [
        'Jahitan rapi dan konsisten',
        'Tidak ada benang sisa (trimming)',
        'Warna sesuai standar',
        'Ukuran sesuai spesifikasi',
        'Label dan tag terpasang',
        'Kemasan sesuai standar',
        'Tidak ada cacat fisik (robek/noda)',
        'Kancing/zipper berfungsi normal',
    ];

    public function index() {
        $inspections = QcInspection::with(['order.product','inspector','handover'])->latest()->paginate(15);
        return view('qc.index', compact('inspections'));
    }

    public function create(Request $request) {
        $orders   = ProductionOrder::with('product')->whereIn('status',['active','draft'])->latest()->get();
        $checklist = self::CHECKLIST;
        $handover  = null;

        if ($request->handover_id) {
            $handover = Handover::with(['order.product','toStation'])->find($request->handover_id);
        }

        return view('qc.create', compact('orders','checklist','handover'));
    }

    public function store(Request $request) {
        $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'handover_id'         => 'nullable|exists:handovers,id',
            'total_checked'       => 'required|integer|min:1',
            'total_defect'        => 'required|integer|min:0',
            'notes'               => 'nullable|string',
            'photo_evidence'      => 'nullable|image|max:10240',
            'checklist'           => 'nullable|array',
        ]);

        $totalChecked = (int) $request->total_checked;
        $totalDefect  = (int) $request->total_defect;
        $defectRate   = $totalChecked > 0 ? round(($totalDefect / $totalChecked) * 100, 2) : 0;
        $status       = $defectRate == 0 ? 'pass' : ($defectRate <= 5 ? 'conditional' : 'fail');

        $photoPath = null;
        if ($request->hasFile('photo_evidence')) {
            $photoPath = $this->compressAndStore($request->file('photo_evidence'), 'qc');
        }

        $inspection = QcInspection::create([
            'production_order_id' => $request->production_order_id,
            'handover_id'         => $request->handover_id ?: null,
            'inspector_id'        => auth()->id(),
            'inspected_at'        => now(),
            'status'              => $status,
            'total_checked'       => $totalChecked,
            'total_defect'        => $totalDefect,
            'defect_rate'         => $defectRate,
            'notes'               => $request->notes,
            'photo_evidence'      => $photoPath,
        ]);

        foreach (self::CHECKLIST as $i => $item) {
            QcChecklistItem::create([
                'qc_inspection_id' => $inspection->id,
                'checklist_item'   => $item,
                'result'           => $request->input("checklist.{$i}.result", 'ok'),
                'notes'            => $request->input("checklist.{$i}.notes"),
            ]);
        }

        // Jika QC FAIL → notifikasi supervisor & PIC QC
        if ($status === 'fail') {
            $admins = User::whereIn('role', ['admin','supervisor'])->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => "QC FAIL: {$inspection->order->order_no}",
                    'message' => "Inspeksi QC order {$inspection->order->order_no} GAGAL. Defect rate: {$defectRate}%. Perlu tindak lanjut.",
                    'type'    => 'danger',
                    'link'    => "/qc/{$inspection->id}",
                    'is_read' => false,
                ]);
            }
        }

        $statusLabel = match($status) { 'pass' => 'LOLOS', 'conditional' => 'KONDISIONAL', 'fail' => 'GAGAL' };
        $redirectMsg = "Inspeksi QC berhasil disimpan. Status: {$statusLabel} (defect rate: {$defectRate}%).";

        // Jika terhubung ke handover → redirect kembali ke detail handover
        if ($request->handover_id) {
            return redirect()->route('handover.show', $request->handover_id)->with('success', $redirectMsg);
        }

        return redirect()->route('qc.index')->with('success', $redirectMsg);
    }

    public function show(QcInspection $qc) {
        $qc->load(['order.product','inspector','checklistItems','handover']);
        return view('qc.show', compact('qc'));
    }

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
        $maxW = 800;

        if ($srcW > $maxW) {
            $ratio  = $maxW / $srcW;
            $newW   = $maxW;
            $newH   = (int) round($srcH * $ratio);
            $canvas = imagecreatetruecolor($newW, $newH);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
            imagedestroy($src);
            $src = $canvas;
        }

        imagejpeg($src, $destPath, 75);
        imagedestroy($src);

        return "storage/{$folder}/{$filename}";
    }
}

<?php
namespace App\Http\Controllers;
use App\Models\{QcInspection, QcChecklistItem, ProductionOrder};
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
        $inspections = QcInspection::with(['order.product','inspector'])->latest()->paginate(15);
        return view('qc.index', compact('inspections'));
    }

    public function create() {
        $orders = ProductionOrder::with('product')->whereIn('status',['active','draft'])->latest()->get();
        $checklist = self::CHECKLIST;
        return view('qc.create', compact('orders','checklist'));
    }

    public function store(Request $request) {
        $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'total_checked' => 'required|integer|min:1',
            'total_defect' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'photo_evidence' => 'nullable|image|max:10240',
            'checklist' => 'nullable|array',
        ]);

        $totalChecked = (int) $request->total_checked;
        $totalDefect  = (int) $request->total_defect;
        $defectRate   = $totalChecked > 0 ? round(($totalDefect / $totalChecked) * 100, 2) : 0;
        $status = $defectRate == 0 ? 'pass' : ($defectRate <= 5 ? 'conditional' : 'fail');

        $photoPath = null;
        if ($request->hasFile('photo_evidence')) {
            $photoPath = $this->compressAndStore($request->file('photo_evidence'), 'qc');
        }

        $inspection = QcInspection::create([
            'production_order_id' => $request->production_order_id,
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

        return redirect()->route('qc.index')->with('success', "Inspeksi QC berhasil disimpan. Status: {$status}");
    }

    public function show(QcInspection $qc) {
        $qc->load(['order.product','inspector','checklistItems']);
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

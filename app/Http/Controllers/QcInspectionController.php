<?php
namespace App\Http\Controllers;

use App\Models\{QcInspection, QcChecklistItem, ProductionOrder, User};
use Illuminate\Http\Request;

class QcInspectionController extends Controller
{
    const CHECKLIST = [
        'Jahitan rapi dan konsisten',
        'Tidak ada benang sisa (trimming)',
        'Warna sesuai standar',
        'Ukuran sesuai spesifikasi',
        'Label dan tag terpasang',
        'Kemasan sesuai standar',
        'Tidak ada cacat fisik (robek/noda)',
        'Kancing/zipper berfungsi normal',
    ];

    public function index()
    {
        $inspections = QcInspection::with(['order.product', 'inspector'])->latest()->paginate(15);
        return view('qc.index', compact('inspections'));
    }

    public function create()
    {
        $orders = ProductionOrder::with('product')->whereIn('status', ['active', 'draft'])->orderBy('order_no')->get();
        $users  = User::orderBy('name')->get();
        $checklist = self::CHECKLIST;
        return view('qc.create', compact('orders', 'users', 'checklist'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_order_id' => 'required|exists:production_orders,id',
            'inspector_id'        => 'required|exists:users,id',
            'inspected_at'        => 'required|date',
            'total_checked'       => 'required|integer|min:1',
            'total_defect'        => 'required|integer|min:0',
            'notes'               => 'nullable|string',
            'photo_evidence'      => 'nullable|image|max:10240',
            'checklist'           => 'nullable|array',
            'checklist.*.result'  => 'nullable|in:ok,fail,na',
            'checklist.*.notes'   => 'nullable|string',
        ]);

        $defectRate = round(($request->total_defect / $request->total_checked) * 100, 2);
        $status = match(true) {
            $defectRate == 0       => 'pass',
            $defectRate > 5        => 'fail',
            default                => 'conditional',
        };

        $photoPath = null;
        if ($request->hasFile('photo_evidence')) {
            $photoPath = $this->compressAndStore($request->file('photo_evidence'), 'qc-photos');
        }

        $inspection = QcInspection::create([
            'production_order_id' => $request->production_order_id,
            'inspector_id'        => $request->inspector_id,
            'inspected_at'        => $request->inspected_at,
            'status'              => $status,
            'total_checked'       => $request->total_checked,
            'total_defect'        => $request->total_defect,
            'defect_rate'         => $defectRate,
            'notes'               => $request->notes,
            'photo_evidence'      => $photoPath,
        ]);

        foreach (self::CHECKLIST as $i => $item) {
            QcChecklistItem::create([
                'qc_inspection_id' => $inspection->id,
                'checklist_item'   => $item,
                'result'           => $request->input("checklist.{$i}.result", 'na'),
                'notes'            => $request->input("checklist.{$i}.notes"),
            ]);
        }

        return redirect()->route('qc.show', $inspection)->with('success', 'Inspeksi QC berhasil disimpan.');
    }

    public function show(QcInspection $qc)
    {
        $qc->load(['order.product', 'inspector', 'items']);
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
        $srcW = imagesx($src); $srcH = imagesy($src); $maxW = 1920;
        if ($srcW > $maxW) {
            $ratio = $maxW / $srcW; $newW = $maxW; $newH = (int) round($srcH * $ratio);
            $canvas = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
            imagedestroy($src); $src = $canvas;
        }
        imagejpeg($src, $destPath, 80); imagedestroy($src);
        return "storage/{$folder}/{$filename}";
    }
}

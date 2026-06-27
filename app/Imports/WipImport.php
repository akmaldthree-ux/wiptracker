<?php
namespace App\Imports;

use App\Models\WipEntry;
use App\Models\ProductionOrder;
use App\Models\Sku;
use App\Models\Station;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

use Illuminate\Support\Facades\Auth;

class WipImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $imported = 0;
    public array $notFound = [];

    public function model(array $row): ?WipEntry
    {
        if (empty($row['no_order']) || empty($row['kode_stasiun'])) return null;

        $order = ProductionOrder::where('order_no', trim($row['no_order']))->first();
        $station = Station::where('code', strtoupper(trim($row['kode_stasiun'])))->first();

        if (!$order) {
            $this->notFound[] = "Order '{$row['no_order']}' tidak ditemukan";
            return null;
        }
        if (!$station) {
            $this->notFound[] = "Stasiun '{$row['kode_stasiun']}' tidak ditemukan";
            return null;
        }

        $sku = null;
        if (!empty($row['kode_warna']) && !empty($row['kode_ukuran'])) {
            $sku = Sku::whereHas('color', fn($q) => $q->where('code', strtoupper(trim($row['kode_warna']))))
                       ->whereHas('size', fn($q) => $q->where('name', trim($row['kode_ukuran'])))
                       ->whereHas('series', fn($q) => $q->where('product_id', $order->product_id))
                       ->first();
        }

        if (!$sku) {
            $this->notFound[] = "SKU '{$row['kode_warna']}/{$row['kode_ukuran']}' untuk order '{$row['no_order']}' tidak ditemukan";
            return null;
        }

        $this->imported++;

        return new WipEntry([
            'production_order_id' => $order->id,
            'sku_id'              => $sku->id,
            'station_id'          => $station->id,
            'qty_in'              => intval($row['qty_in'] ?? 0),
            'qty_out'             => intval($row['qty_out'] ?? 0),
            'qty_reject'          => intval($row['qty_reject'] ?? 0),
            'input_date'          => !empty($row['tanggal']) ? \Carbon\Carbon::parse($row['tanggal'])->toDateString() : now()->toDateString(),
            'notes'               => trim($row['catatan'] ?? ''),
            'created_by'          => Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'no_order'     => 'required',
            'kode_stasiun' => 'required',
            'kode_warna'   => 'required',
            'kode_ukuran'  => 'required',
            'qty_in'       => 'required|integer|min:0',
        ];
    }
}

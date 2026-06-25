<?php
namespace App\Imports;

use App\Models\ProductionOrderItem;
use App\Models\Sku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class OrderItemImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    public int $imported = 0;
    public array $notFound = [];

    public function __construct(private int $orderId, private int $seriesId) {}

    public function model(array $row): ?ProductionOrderItem
    {
        if (empty($row['kode_warna']) || empty($row['kode_ukuran']) || empty($row['target_qty'])) return null;

        $sku = Sku::whereHas('color', fn($q) => $q->where('code', strtoupper(trim($row['kode_warna']))))
                   ->whereHas('size', fn($q) => $q->where('name', trim($row['kode_ukuran'])))
                   ->where('series_id', $this->seriesId)
                   ->first();

        if (!$sku) {
            $this->notFound[] = "SKU warna='{$row['kode_warna']}' ukuran='{$row['kode_ukuran']}' tidak ditemukan";
            return null;
        }

        $this->imported++;

        return ProductionOrderItem::updateOrCreate(
            ['production_order_id' => $this->orderId, 'sku_id' => $sku->id],
            ['target_qty' => intval($row['target_qty'])]
        );
    }

    public function rules(): array
    {
        return [
            'kode_warna'  => 'required',
            'kode_ukuran' => 'required',
            'target_qty'  => 'required|integer|min:1',
        ];
    }
}

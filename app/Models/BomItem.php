<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $fillable = ['product_id','raw_material_id','qty_per_unit','waste_percentage','notes'];
    protected $casts = ['qty_per_unit'=>'decimal:4','waste_percentage'=>'decimal:2'];

    public function product()     { return $this->belongsTo(Product::class); }
    public function rawMaterial() { return $this->belongsTo(RawMaterial::class); }

    // Qty dibutuhkan termasuk waste
    public function getQtyNeeded(int $orderQty): float
    {
        return $orderQty * $this->qty_per_unit * (1 + $this->waste_percentage / 100);
    }
}

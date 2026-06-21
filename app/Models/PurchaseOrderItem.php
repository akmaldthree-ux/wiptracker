<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id','raw_material_id','qty_ordered','qty_received','unit_price','total_price','notes'];
    protected $casts = ['qty_ordered'=>'decimal:2','qty_received'=>'decimal:2','unit_price'=>'decimal:2','total_price'=>'decimal:2'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function rawMaterial()   { return $this->belongsTo(RawMaterial::class); }

    public function getRemainingAttribute(): float {
        return max(0, $this->qty_ordered - $this->qty_received);
    }
}

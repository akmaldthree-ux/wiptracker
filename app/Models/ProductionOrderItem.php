<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionOrderItem extends Model {
    protected $fillable = ['production_order_id','sku_id','target_qty'];
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function sku() { return $this->belongsTo(Sku::class); }
}

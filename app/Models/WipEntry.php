<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WipEntry extends Model {
    protected $fillable = ['production_order_id','sku_id','station_id','qty_in','qty_out','qty_reject','input_date','notes','created_by'];
    protected $casts = ['input_date' => 'date'];
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function sku() { return $this->belongsTo(Sku::class); }
    public function station() { return $this->belongsTo(Station::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function getQtyInProcessAttribute() { return max(0, $this->qty_in - $this->qty_out - $this->qty_reject); }
}

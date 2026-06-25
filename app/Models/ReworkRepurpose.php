<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ReworkRepurpose extends Model
{
    protected $fillable = ['rework_handover_id','original_order_id','new_order_id','sku_id','qty','notes','created_by'];

    public function reworkHandover()  { return $this->belongsTo(Handover::class, 'rework_handover_id'); }
    public function originalOrder()   { return $this->belongsTo(ProductionOrder::class, 'original_order_id'); }
    public function newOrder()        { return $this->belongsTo(ProductionOrder::class, 'new_order_id'); }
    public function sku()             { return $this->belongsTo(Sku::class); }
    public function creator()         { return $this->belongsTo(User::class, 'created_by'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HandoverItem extends Model {
    protected $fillable = ['handover_id','sku_id','qty_sent','qty_received','qty_reject','reject_notes','discrepancy','discrepancy_notes'];
    public function handover() { return $this->belongsTo(Handover::class); }
    public function sku() { return $this->belongsTo(Sku::class); }
}

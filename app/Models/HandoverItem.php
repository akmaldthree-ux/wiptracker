<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class HandoverItem extends Model {
    protected $fillable = ['handover_id','sku_id','qty_sent','qty_received','qty_reject','reject_type','reject_notes','photo_reject','discrepancy','discrepancy_notes','rework_to_station_id'];
    public function handover()        { return $this->belongsTo(Handover::class); }
    public function sku()             { return $this->belongsTo(Sku::class); }
    public function reworkToStation() { return $this->belongsTo(Station::class, 'rework_to_station_id'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QcInspection extends Model {
    protected $fillable = ['production_order_id','handover_id','inspector_id','inspected_at','status','total_checked','total_defect','defect_rate','notes','photo_evidence'];
    protected $casts = ['inspected_at' => 'datetime'];
    public function order()    { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function handover() { return $this->belongsTo(Handover::class); }
    public function inspector(){ return $this->belongsTo(User::class, 'inspector_id'); }
    public function checklistItems() { return $this->hasMany(QcChecklistItem::class); }
}

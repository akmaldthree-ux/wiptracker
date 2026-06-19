<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Handover extends Model {
    protected $fillable = ['handover_no','production_order_id','from_station_id','to_station_id','sewing_location_id','status','initiated_by','confirmed_by','approved_by','notes','condition_notes','photo_sent','photo_received','initiated_at','confirmed_at'];
    protected $casts = ['initiated_at' => 'datetime', 'confirmed_at' => 'datetime'];
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function fromStation() { return $this->belongsTo(Station::class, 'from_station_id'); }
    public function toStation() { return $this->belongsTo(Station::class, 'to_station_id'); }
    public function initiatedBy() { return $this->belongsTo(User::class, 'initiated_by'); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items() { return $this->hasMany(HandoverItem::class); }
    public function sewingLocation() { return $this->belongsTo(SewingLocation::class); }
    public function getTotalSentAttribute() { return $this->items->sum('qty_sent'); }
    public function getTotalReceivedAttribute() { return $this->items->whereNotNull('qty_received')->sum('qty_received'); }
    public function getTotalDiscrepancyAttribute() { return $this->items->whereNotNull('discrepancy')->sum('discrepancy'); }
    public function hasDiscrepancy(): bool { return $this->items->whereNotNull('discrepancy')->where('discrepancy', '!=', 0)->count() > 0; }
    public function getStatusColorAttribute() {
        return match($this->status) {
            'pending' => 'warning', 'confirmed' => 'success',
            'discrepancy' => 'danger', 'approved' => 'info', default => 'secondary',
        };
    }
    public function getStatusLabelAttribute() {
        return match($this->status) {
            'pending' => 'Menunggu Konfirmasi', 'confirmed' => 'Dikonfirmasi',
            'discrepancy' => 'Ada Selisih', 'approved' => 'Disetujui', default => $this->status,
        };
    }
}

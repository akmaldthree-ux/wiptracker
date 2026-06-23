<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionOrder extends Model {
    protected $fillable = ['order_no','product_id','series_id','target_date','status','selling_price','notes','created_by','materials_approved','materials_approved_by','materials_approved_at'];
    protected $casts = ['target_date' => 'date', 'materials_approved' => 'boolean', 'materials_approved_at' => 'datetime'];
    public function product() { return $this->belongsTo(Product::class); }
    public function series() { return $this->belongsTo(Series::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(ProductionOrderItem::class); }
    public function wipEntries() { return $this->hasMany(WipEntry::class); }
    public function handovers() { return $this->hasMany(Handover::class); }
    public function budget() { return $this->hasOne(Budget::class); }
    public function cuttingPlans() { return $this->hasMany(CuttingPlan::class); }
    public function materialAllocations() { return $this->hasMany(MaterialAllocation::class); }
    public function costEntries() { return $this->hasMany(CostEntry::class); }
    public function stationDeadlines() { return $this->hasMany(OrderStationDeadline::class)->with('station')->orderBy('target_date'); }
    public function materialRequirements() { return $this->hasMany(MaterialRequirement::class); }
    public function materialsApprovedBy() { return $this->belongsTo(User::class, 'materials_approved_by'); }

    public function hasMaterialRequirements(): bool { return $this->materialRequirements()->exists(); }
    public function hasMaterialShortage(): bool
    {
        return $this->materialRequirements->contains(fn($r) => $r->qty_shortage > 0);
    }

    public function getTotalTargetQty() { return $this->items->sum('target_qty'); }
    public function getStatusLabelAttribute() {
        return match($this->status) {
            'draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai',
            'on_hold' => 'Ditahan', 'cancelled' => 'Dibatalkan', default => $this->status,
        };
    }
    public function getStatusColorAttribute() {
        return match($this->status) {
            'draft' => 'secondary', 'active' => 'primary', 'completed' => 'success',
            'on_hold' => 'warning', 'cancelled' => 'danger', default => 'secondary',
        };
    }
    public function isOverdue(): bool {
        return $this->target_date < now() && !in_array($this->status, ['completed','cancelled']);
    }
    public function getProgressPercentage(): int {
        $total = $this->getTotalTargetQty();
        if ($total == 0) return 0;
        $warehouseStation = Station::where('code','WH')->first();
        if (!$warehouseStation) return 0;
        $done = WipEntry::where('production_order_id', $this->id)
            ->where('station_id', $warehouseStation->id)->sum('qty_out');
        return min(100, (int)(($done / $total) * 100));
    }
}

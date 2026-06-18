<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuttingPlan extends Model
{
    protected $fillable = [
        'plan_no', 'production_order_id', 'planned_date', 'marker_length',
        'fabric_width', 'total_layers', 'planned_qty', 'actual_qty',
        'efficiency', 'shift', 'notes', 'status', 'created_by',
    ];

    protected $casts = ['planned_date' => 'date'];

    public function order()      { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function bundles()    { return $this->hasMany(CuttingBundle::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'       => 'secondary',
            'scheduled'   => 'primary',
            'in_progress' => 'warning',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'       => 'Draft',
            'scheduled'   => 'Terjadwal',
            'in_progress' => 'Sedang Dipotong',
            'completed'   => 'Selesai',
            'cancelled'   => 'Dibatalkan',
            default       => $this->status,
        };
    }

    // Estimasi kebutuhan kain dalam meter (marker_length × total_layers)
    public function getFabricNeededAttribute(): ?float
    {
        if (!$this->marker_length || !$this->total_layers) return null;
        return round($this->marker_length * $this->total_layers, 2);
    }

    public function getProgressAttribute(): int
    {
        if (!$this->planned_qty || !$this->actual_qty) return 0;
        return min(100, (int) round(($this->actual_qty / $this->planned_qty) * 100));
    }
}

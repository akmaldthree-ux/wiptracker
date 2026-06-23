<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderStationDeadline extends Model
{
    protected $fillable = ['production_order_id','station_id','target_date','notes'];
    protected $casts = ['target_date' => 'date'];

    public function order()   { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function station() { return $this->belongsTo(Station::class); }

    public function getDaysRemainingAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->target_date, false);
    }

    public function getIsOverdueAttribute(): bool { return $this->days_remaining < 0; }

    public function getStatusAttribute(): string
    {
        if ($this->days_remaining < 0)  return 'overdue';
        if ($this->days_remaining <= 2) return 'critical';
        if ($this->days_remaining <= 5) return 'warning';
        return 'ontrack';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'overdue'  => 'danger',
            'critical' => 'danger',
            'warning'  => 'warning',
            default    => 'success',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->days_remaining < 0)  return abs($this->days_remaining) . ' hari terlambat';
        if ($this->days_remaining === 0) return 'Deadline hari ini';
        return $this->days_remaining . ' hari lagi';
    }
}

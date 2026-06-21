<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $fillable = ['name', 'code', 'order_sequence', 'description', 'bottleneck_threshold', 'is_active', 'is_final'];
    protected $casts = ['is_active' => 'boolean', 'is_final' => 'boolean'];

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function wipEntries(): HasMany { return $this->hasMany(WipEntry::class); }
    public function handoversFrom(): HasMany { return $this->hasMany(Handover::class, 'from_station_id'); }
    public function handoversTo(): HasMany { return $this->hasMany(Handover::class, 'to_station_id'); }
    public function nextStation() { return Station::where('order_sequence', $this->order_sequence + 1)->where('is_active', true)->first(); }

    public function getCurrentWipCount($orderId = null) {
        $q = WipEntry::where('station_id', $this->id);
        if ($orderId) $q->where('production_order_id', $orderId);
        return $q->sum('qty_in') - $q->sum('qty_out') - $q->sum('qty_reject');
    }

    public function isBottleneck($orderId = null): bool {
        return $this->getCurrentWipCount($orderId) > $this->bottleneck_threshold;
    }
}

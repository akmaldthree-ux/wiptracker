<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SecondStock extends Model
{
    protected $table = 'second_stock';
    protected $fillable = ['production_order_id','sku_id','from_station_id','handover_item_id','qty','status','discount_price','notes','created_by'];

    public function order()       { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function sku()         { return $this->belongsTo(Sku::class); }
    public function fromStation() { return $this->belongsTo(Station::class, 'from_station_id'); }
    public function handoverItem(){ return $this->belongsTo(HandoverItem::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => 'Tersedia',
            'sold'      => 'Terjual',
            'scrapped'  => 'Discrap',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'warning',
            'sold'      => 'success',
            'scrapped'  => 'secondary',
            default     => 'secondary',
        };
    }
}

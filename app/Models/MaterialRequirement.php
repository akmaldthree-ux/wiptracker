<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialRequirement extends Model
{
    protected $fillable = ['production_order_id','raw_material_id','qty_needed'];
    protected $casts    = ['qty_needed' => 'decimal:4'];

    public function order()       { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function rawMaterial() { return $this->belongsTo(RawMaterial::class); }

    public function getQtyAvailableAttribute(): float
    {
        return (float) $this->rawMaterial->current_stock;
    }

    public function getQtyShortageAttribute(): float
    {
        return max(0, (float) $this->qty_needed - $this->qty_available);
    }

    public function getIssufficientAttribute(): bool
    {
        return $this->qty_shortage <= 0;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_sufficient ? 'success' : 'danger';
    }
}

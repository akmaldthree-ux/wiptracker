<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RawMaterial extends Model {
    protected $fillable = ['code','name','unit','category','color','min_stock','current_stock','unit_price','is_active','supplier_id'];
    protected $casts = ['is_active' => 'boolean', 'min_stock' => 'decimal:2', 'current_stock' => 'decimal:2', 'unit_price' => 'decimal:2'];
    public function receipts() { return $this->hasMany(MaterialReceipt::class); }
    public function allocations() { return $this->hasMany(MaterialAllocation::class); }
    public function isBelowMinStock(): bool { return $this->current_stock < $this->min_stock; }
    public function getCategoryLabelAttribute() {
        return match($this->category) {
            'kain' => 'Kain', 'benang' => 'Benang', 'aksesoris' => 'Aksesoris', default => 'Lainnya',
        };
    }
}

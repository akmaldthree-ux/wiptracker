<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialAllocation extends Model {
    protected $fillable = ['raw_material_id','production_order_id','qty_planned','qty_actual','allocation_date','notes','created_by'];
    protected $casts = ['allocation_date' => 'date', 'qty_planned' => 'decimal:2', 'qty_actual' => 'decimal:2'];
    public function material() { return $this->belongsTo(RawMaterial::class, 'raw_material_id'); }
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

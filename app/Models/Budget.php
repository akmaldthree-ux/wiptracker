<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Budget extends Model {
    protected $fillable = ['production_order_id','material_cost_plan','process_cost_plan','overhead_cost_plan','total_plan','material_cost_actual','process_cost_actual','overhead_cost_actual','total_actual','created_by'];
    protected $casts = ['material_cost_plan'=>'decimal:2','process_cost_plan'=>'decimal:2','overhead_cost_plan'=>'decimal:2','total_plan'=>'decimal:2','material_cost_actual'=>'decimal:2','process_cost_actual'=>'decimal:2','overhead_cost_actual'=>'decimal:2','total_actual'=>'decimal:2'];
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function getVarianceAttribute() { return $this->total_plan - $this->total_actual; }
    public function getVariancePercentAttribute() { return $this->total_plan > 0 ? (($this->total_plan - $this->total_actual) / $this->total_plan) * 100 : 0; }
    public function getUtilisasiPercentAttribute() { return $this->total_plan > 0 ? ($this->total_actual / $this->total_plan) * 100 : 0; }
    public function isOverBudget(): bool { return $this->total_actual > $this->total_plan; }
}

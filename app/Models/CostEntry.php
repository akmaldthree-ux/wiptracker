<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CostEntry extends Model {
    protected $fillable = ['production_order_id','budget_id','type','description','amount','station_id','entry_date','created_by'];
    protected $casts = ['entry_date' => 'date', 'amount' => 'decimal:2'];
    public function order() { return $this->belongsTo(ProductionOrder::class, 'production_order_id'); }
    public function budget() { return $this->belongsTo(Budget::class); }
    public function station() { return $this->belongsTo(Station::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function getTypeLabelAttribute() {
        return match($this->type) { 'material'=>'Bahan Baku','process'=>'Proses Produksi','overhead'=>'Overhead', default=>$this->type };
    }
}

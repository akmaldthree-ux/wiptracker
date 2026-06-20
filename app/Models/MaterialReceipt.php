<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialReceipt extends Model {
    protected $fillable = ['raw_material_id','supplier_id','qty','unit_price','total_price','supplier','po_no','receipt_date','confirmed_by','notes'];
    protected $casts = ['receipt_date' => 'date', 'qty' => 'decimal:2', 'unit_price' => 'decimal:2', 'total_price' => 'decimal:2'];
    public function material() { return $this->belongsTo(RawMaterial::class, 'raw_material_id'); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function supplierRelation() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['po_no','supplier_id','status','order_date','expected_date','total_amount','notes','created_by','sent_at','received_at'];
    protected $casts = ['order_date'=>'date','expected_date'=>'date','sent_at'=>'datetime','received_at'=>'datetime','total_amount'=>'decimal:2'];

    public function supplier()  { return $this->belongsTo(Supplier::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function items()     { return $this->hasMany(PurchaseOrderItem::class); }

    public function getStatusLabelAttribute(): string {
        return match($this->status) {
            'draft'     => 'Draft',
            'sent'      => 'Terkirim ke Supplier',
            'partial'   => 'Diterima Sebagian',
            'received'  => 'Diterima Lengkap',
            'cancelled' => 'Dibatalkan',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string {
        return match($this->status) {
            'draft'     => 'secondary',
            'sent'      => 'primary',
            'partial'   => 'warning',
            'received'  => 'success',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }

    public function getReceiveProgressAttribute(): int {
        $ordered  = $this->items->sum('qty_ordered');
        $received = $this->items->sum('qty_received');
        return $ordered > 0 ? (int) round($received / $ordered * 100) : 0;
    }
}

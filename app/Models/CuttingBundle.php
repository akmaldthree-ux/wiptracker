<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuttingBundle extends Model
{
    protected $fillable = ['bundle_no', 'cutting_plan_id', 'sku_id', 'qty', 'status', 'notes'];

    public function plan() { return $this->belongsTo(CuttingPlan::class, 'cutting_plan_id'); }
    public function sku()  { return $this->belongsTo(Sku::class); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'cut'            => 'Sudah Dipotong',
            'bundled'        => 'Sudah Di-bundle',
            'sent_to_sewing' => 'Terkirim ke Sewing',
            default          => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'cut'            => 'secondary',
            'bundled'        => 'info',
            'sent_to_sewing' => 'success',
            default          => 'secondary',
        };
    }
}

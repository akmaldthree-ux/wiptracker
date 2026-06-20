<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QcChecklistItem extends Model
{
    protected $fillable = ['qc_inspection_id', 'checklist_item', 'result', 'notes'];

    public function inspection() { return $this->belongsTo(QcInspection::class, 'qc_inspection_id'); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = ['name','code','phone','email','address','contact_person','notes','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function materialReceipts() { return $this->hasMany(MaterialReceipt::class); }
}

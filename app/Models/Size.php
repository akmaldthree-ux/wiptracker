<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Size extends Model {
    protected $fillable = ['name','type','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function skus() { return $this->hasMany(Sku::class); }
}

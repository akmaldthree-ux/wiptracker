<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Color extends Model {
    protected $fillable = ['name','code','hex_code','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function skus() { return $this->hasMany(Sku::class); }
}

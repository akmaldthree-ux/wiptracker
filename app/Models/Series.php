<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Series extends Model {
    protected $fillable = ['name','code','product_id','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function product() { return $this->belongsTo(Product::class); }
    public function skus() { return $this->hasMany(Sku::class); }
}

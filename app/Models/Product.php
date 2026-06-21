<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['code','name','category','description','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function series() { return $this->hasMany(Series::class); }
    public function bomItems() { return $this->hasMany(BomItem::class); }
    public function skus() { return $this->hasMany(Sku::class); }
    public function productionOrders() { return $this->hasMany(ProductionOrder::class); }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sku extends Model {
    protected $fillable = ['product_id','series_id','color_id','size_id','sku_code','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function product() { return $this->belongsTo(Product::class); }
    public function series() { return $this->belongsTo(Series::class); }
    public function color() { return $this->belongsTo(Color::class); }
    public function size() { return $this->belongsTo(Size::class); }
    public function wipEntries() { return $this->hasMany(WipEntry::class); }
    public function getFullNameAttribute() {
        return ($this->product?->name ?? '?') . ' - ' . ($this->series?->name ?? '?') . ' / ' . ($this->color?->name ?? '?') . ' / ' . ($this->size?->name ?? '?');
    }
}

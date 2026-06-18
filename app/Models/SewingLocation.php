<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewingLocation extends Model
{
    protected $fillable = ['code', 'name', 'address', 'description', 'capacity', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function handovers()
    {
        return $this->hasMany(Handover::class);
    }
}

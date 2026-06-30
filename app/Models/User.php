<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'station_id', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function wipEntries(): HasMany
    {
        return $this->hasMany(WipEntry::class, 'created_by');
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class)->where('is_read', false);
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isSupervisor(): bool { return in_array($this->role, ['admin', 'supervisor']); }
    public function isPIC(): bool { return $this->role === 'pic_stasiun'; }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isStaffGudang(): bool { return $this->role === 'staff_gudang'; }
    public function isProcurement(): bool { return in_array($this->role, ['procurement', 'admin']); }
    public function isStaffOrAbove(): bool { return in_array($this->role, ['admin', 'supervisor', 'staff_gudang']); }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'       => 'Admin',
            'supervisor'  => 'Supervisor Produksi',
            'pic_stasiun' => 'PIC Stasiun',
            'manager'     => 'Manager / Owner',
            'staff_gudang'=> 'Staff Gudang',
            'procurement' => 'Tim Procurement',
            default       => ucfirst($this->role),
        };
    }
}

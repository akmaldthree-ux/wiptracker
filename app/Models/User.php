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
        'name', 'email', 'password', 'role', 'roles', 'station_id', 'phone', 'is_active',
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
            'roles'     => 'array',
        ];
    }

    /** Returns the effective roles array (multi-role support) */
    public function getRolesListAttribute(): array
    {
        // Use `roles` JSON column if set, fall back to legacy `role` string
        if (!empty($this->roles)) return $this->roles;
        return $this->role ? [$this->role] : [];
    }

    public function hasAnyRole(array $check): bool
    {
        return !empty(array_intersect($this->roles_list, $check));
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

    public function isAdmin(): bool        { return $this->hasAnyRole(['admin']); }
    public function isSupervisor(): bool   { return $this->hasAnyRole(['admin', 'supervisor']); }
    public function isPIC(): bool          { return $this->hasAnyRole(['pic_stasiun']); }
    public function isManager(): bool      { return $this->hasAnyRole(['manager']); }
    public function isStaffGudang(): bool  { return $this->hasAnyRole(['staff_gudang']); }
    public function isProcurement(): bool  { return $this->hasAnyRole(['procurement', 'admin']); }
    public function isStaffOrAbove(): bool { return $this->hasAnyRole(['admin', 'supervisor', 'staff_gudang']); }
    public function isIE(): bool           { return $this->hasAnyRole(['ie', 'admin']); }
    public function isPPIC(): bool         { return $this->hasAnyRole(['ppic', 'admin']); }
    /** Can edit/create/delete master data */
    public function canManageMaster(): bool { return $this->hasAnyRole(['admin', 'supervisor', 'ppic']); }
    /** Can access budget */
    public function canAccessBudget(): bool { return $this->hasAnyRole(['admin', 'supervisor', 'manager', 'ppic']); }
    /** Can access procurement & purchase order */
    public function canAccessProcurement(): bool { return $this->hasAnyRole(['admin', 'supervisor', 'procurement', 'ppic']); }
    /** Can access BOM */
    public function canAccessBom(): bool { return $this->hasAnyRole(['admin', 'supervisor', 'ie', 'ppic']); }

    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin'         => 'Admin',
            'supervisor'    => 'Supervisor Produksi',
            'pic_stasiun'   => 'PIC Stasiun',
            'manager'       => 'Manager / Owner',
            'staff_gudang'  => 'Staff Gudang',
            'procurement'   => 'Tim Procurement',
            'staff_produksi'=> 'Staff Produksi',
            'ie'            => 'Industrial Engineering',
            'ppic'          => 'PPIC',
        ];
        return implode(' + ', array_map(fn($r) => $labels[$r] ?? ucfirst($r), $this->roles_list));
    }
}

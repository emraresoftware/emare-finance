<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'branch_id',
        'role_id',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Kullanıcının süper admin olup olmadığını kontrol eder.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /* ─── Tenant / Branch İlişkileri ─── */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /* ─── RBAC İlişkileri ─── */

    public function primaryRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('tenant_id', 'branch_id')
            ->using(UserRole::class);
    }

    /**
     * Kullanıcının belirli bir yetkiye sahip olup olmadığını kontrol eder.
     */
    public function hasPermission(string $permissionCode): bool
    {
        // Süper admin tüm izinlere sahiptir
        if ($this->is_super_admin) {
            return true;
        }

        // Birincil rol kontrolü
        if ($this->primaryRole && $this->primaryRole->hasPermission($permissionCode)) {
            return true;
        }

        // Ek roller kontrolü
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol eder.
     */
    public function hasRole(string $roleCode): bool
    {
        if ($this->primaryRole && $this->primaryRole->code === $roleCode) {
            return true;
        }

        return $this->roles()->where('code', $roleCode)->exists();
    }

    /**
     * Kullanıcının bulunduğu branch'te belirli bir modülün aktif olup olmadığını kontrol eder.
     */
    public function hasModule(string $moduleCode): bool
    {
        // Süper admin tüm modüllere erişebilir
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->branch) {
            return $this->branch->hasModule($moduleCode);
        }

        if ($this->tenant) {
            return $this->tenant->hasModule($moduleCode);
        }

        return false;
    }

    /* ─── Mevcut İlişkiler ─── */

    /**
     * Kullanıcının satışları
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}

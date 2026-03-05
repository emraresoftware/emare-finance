<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = [
        'code', 'name', 'description',
        'price_monthly', 'price_yearly',
        'is_active', 'limits', 'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
        'is_active'     => 'boolean',
        'limits'        => 'array',
    ];

    /* ─── İlişkiler ─── */

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'plan_modules')
            ->withPivot('included', 'config')
            ->withTimestamps();
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /* ─── Yardımcı Metodlar ─── */

    public function getLimit(string $key, mixed $default = null): mixed
    {
        return data_get($this->limits, $key, $default);
    }

    public function includedModules(): BelongsToMany
    {
        return $this->modules()->wherePivot('included', true);
    }
}

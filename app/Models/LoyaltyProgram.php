<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'description',
        'points_per_currency', 'currency_per_point',
        'min_redeem_points', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_per_currency' => 'decimal:2',
        'currency_per_point' => 'decimal:4',
    ];

    public function points() { return $this->hasMany(LoyaltyPoint::class); }
}

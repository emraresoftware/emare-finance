<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'customer_id', 'loyalty_program_id', 'points', 'type',
        'description', 'sale_id', 'campaign_id', 'balance_after', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function program() { return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id'); }
    public function sale() { return $this->belongsTo(Sale::class); }
    public function campaign() { return $this->belongsTo(Campaign::class); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'earn' => 'Kazanım',
            'redeem' => 'Harcama',
            'expire' => 'Süre Dolumu',
            'bonus' => 'Bonus',
            'adjustment' => 'Düzenleme',
            default => $this->type,
        };
    }
}

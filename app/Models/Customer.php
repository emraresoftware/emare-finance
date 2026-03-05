<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id', 'name', 'type', 'tax_number', 'tax_office',
        'phone', 'email', 'address', 'city', 'district',
        'balance', 'notes', 'is_active', 'birth_date',
    ];

    protected $casts = [
        'balance'    => 'decimal:2',
        'is_active'  => 'boolean',
        'birth_date' => 'date',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        $prefix = $this->balance >= 0 ? 'Alacak' : 'Borç';
        return $prefix . ': ₺' . number_format(abs($this->balance), 2, ',', '.');
    }
}

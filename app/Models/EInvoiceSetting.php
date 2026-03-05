<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EInvoiceSetting extends Model
{
    protected $table = 'e_invoice_settings';

    protected $fillable = [
        'company_name', 'tax_number', 'tax_office',
        'address', 'city', 'district', 'phone', 'email', 'web',
        'integrator', 'api_key', 'api_secret',
        'sender_alias', 'receiver_alias',
        'auto_send', 'is_active',
        'default_scenario', 'default_currency', 'default_vat_rate',
        'invoice_prefix', 'invoice_counter', 'meta',
    ];

    protected $casts = [
        'auto_send' => 'boolean',
        'is_active' => 'boolean',
        'default_vat_rate' => 'integer',
        'invoice_counter' => 'integer',
        'meta' => 'array',
    ];

    protected $hidden = [
        'api_key', 'api_secret',
    ];

    /**
     * Tekil ayar kaydını getir veya oluştur
     */
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'default_scenario' => 'basic',
            'default_currency' => 'TRY',
            'default_vat_rate' => 20,
            'invoice_counter' => 1,
        ]);
    }
}

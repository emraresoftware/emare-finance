<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsSetting extends Model
{
    protected $fillable = [
        'tenant_id', 'provider', 'api_key', 'api_secret',
        'sender_id', 'username', 'password', 'api_url',
        'balance', 'is_active', 'extra_config',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'balance'      => 'decimal:2',
        'extra_config' => 'array',
    ];

    protected $hidden = ['api_key', 'api_secret', 'password'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function getProviderOptions(): array
    {
        return [
            'netgsm'        => 'NetGSM',
            'iletimerkezi'  => 'İleti Merkezi',
            'twilio'        => 'Twilio',
            'mutlucell'     => 'Mutlucell',
            'jetsms'        => 'JetSMS',
            'custom'        => 'Özel API',
        ];
    }

    public function getProviderLabelAttribute(): string
    {
        return self::getProviderOptions()[$this->provider] ?? $this->provider;
    }
}

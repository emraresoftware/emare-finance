<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBlacklist extends Model
{
    protected $table = 'sms_blacklist';

    protected $fillable = [
        'tenant_id', 'phone', 'reason',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

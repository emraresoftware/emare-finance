<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSegment extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'description', 'color', 'icon',
        'type', 'conditions', 'customer_count', 'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    public function members() { return $this->belongsToMany(Customer::class, 'customer_segment_members', 'segment_id', 'customer_id')->withPivot('added_at'); }

    public function messages() { return $this->hasMany(MarketingMessage::class, 'segment_id'); }

    public function refreshCount(): void
    {
        $this->update(['customer_count' => $this->members()->count()]);
    }
}

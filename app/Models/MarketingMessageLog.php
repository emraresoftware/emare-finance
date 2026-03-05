<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingMessageLog extends Model
{
    protected $fillable = [
        'message_id', 'customer_id', 'recipient', 'status',
        'sent_at', 'delivered_at', 'opened_at', 'clicked_at', 'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function message() { return $this->belongsTo(MarketingMessage::class, 'message_id'); }
    public function customer() { return $this->belongsTo(Customer::class); }
}

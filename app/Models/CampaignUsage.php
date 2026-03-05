<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignUsage extends Model
{
    protected $fillable = ['campaign_id', 'customer_id', 'sale_id', 'discount_applied'];

    protected $casts = ['discount_applied' => 'decimal:2'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function sale() { return $this->belongsTo(Sale::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCount extends Model
{
    protected $fillable = [
        'branch_id', 'status', 'total_items', 'notes', 'counted_at',
    ];

    protected $casts = [
        'counted_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(StockCountItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'external_id', 'name', 'role', 'branch_id',
        'phone', 'email', 'total_sales', 'total_transactions', 'is_active',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function motions()
    {
        return $this->hasMany(StaffMotion::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Personelin satışları (staff_name alanı üzerinden)
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'staff_name', 'name');
    }
}

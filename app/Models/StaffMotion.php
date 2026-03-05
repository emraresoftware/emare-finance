<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMotion extends Model
{
    protected $fillable = [
        'staff_id', 'staff_name', 'action', 'description',
        'application', 'detail', 'action_date',
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}

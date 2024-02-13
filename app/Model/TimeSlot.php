<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $casts = [
        'status'     => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}

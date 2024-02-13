<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $casts = [
        'price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterDevice extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'user_type' => 'string',
        'ip_address' => 'string',
    ];
}

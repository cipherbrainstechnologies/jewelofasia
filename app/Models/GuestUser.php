<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestUser extends Model
{
    use HasFactory;

    protected $casts = [
        'ip_address' => 'string',
        'fcm_token' => 'string',
    ];
}

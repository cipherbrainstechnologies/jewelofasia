<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $casts = [
        'city' => 'integer',
        'user_id' => 'integer',
        'is_guest' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        "zipcode_id" => 'string'
    ];

    protected $fillable = [
        'address_type',
        'contact_person_name',
        'contact_person_number',
        'address',
        'road',
        'house',
        'floor',
        'longitude',
        'latitude',
        'user_id',
        'is_guest',
    ];
}

<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount' => 'float',
        'status' => 'integer',
        'start_date' => 'date',
        'expire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where(['status' => 1])->where('start_date', '<=', now()->format('Y-m-d'))->where('expire_date', '>=', now()->format('Y-m-d'));
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    public function scopeDefault($query)
    {
        return $query->where(['coupon_type' => 'default']);
    }
}

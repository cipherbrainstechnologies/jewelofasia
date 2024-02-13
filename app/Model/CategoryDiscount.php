<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoryDiscount extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'start_date',
        'expire_date',
        'discount_type',
        'discount_amount',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'start_date' => 'date',
        'expire_date' => 'date',
        'discount_type' => 'string',
        'discount_amount' => 'float',
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where(['status' => 1])->where('start_date', '<=', now()->format('Y-m-d'))->where('expire_date', '>=', now()->format('Y-m-d'));
    }
}

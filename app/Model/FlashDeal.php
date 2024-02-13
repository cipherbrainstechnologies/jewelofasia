<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FlashDeal extends Model
{
    protected $table = 'flash_deals';
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'deal_type',
        'status',
        'featured',
        'image',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'featured' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FlashDealProduct::class, 'flash_deal_id');
    }

    public function scopeActive($query)
    {
        return $query->where(['status' => 1])->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('end_date', '>=', date('Y-m-d'));
    }

}

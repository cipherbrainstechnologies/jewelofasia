<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FlashDealProduct extends Model
{
    protected $casts = [
        'product_id'    => 'integer',
        'discount'      => 'float',
        'flash_deal_id' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

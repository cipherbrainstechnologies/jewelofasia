<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SearchedProduct extends Model
{
    protected $table = 'searched_products';

    protected $casts = [
        'recent_search_id' => 'integer',
        'product_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = ['recent_search_id', 'product_id', 'user_id'];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchedData extends Model
{

    protected $casts = [
        'response_data_count' => 'integer',
        'volume' => 'integer',
    ];

    protected $fillable = ['user_id', 'attribute', 'attribute_id', 'response_data_count', 'volume'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function searched_key(): BelongsTo
    {
        return $this->belongsTo(RecentSearch::class, 'attribute_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'attribute_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

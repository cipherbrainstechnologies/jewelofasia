<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class SearchedCategory extends Model
{
    protected $table = 'searched_categories';

    protected $casts = [
        'recent_search_id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = ['recent_search_id', 'category_id', 'user_id'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

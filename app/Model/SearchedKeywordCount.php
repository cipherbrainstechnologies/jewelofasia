<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SearchedKeywordCount extends Model
{
    protected $table = 'searched_keyword_counts';

    protected $casts = [
        'recent_search_id' => 'integer',
        'keyword_count' => 'integer',
    ];

    protected $fillable = ['recent_search_id', 'keyword_count'];
}

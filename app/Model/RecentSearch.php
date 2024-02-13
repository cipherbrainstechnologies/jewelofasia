<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class RecentSearch extends Model
{
    //use SoftDeletes;

    protected $casts = [];

    protected $fillable = [
        'keyword',
    ];

    public function response_data_count(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SearchedData::class, 'attribute_id');
    }

    public function volume(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SearchedKeywordCount::class, 'recent_search_id', 'id');
    }

    public function searched_category(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SearchedCategory::class, 'recent_search_id', 'id');
    }

    public function searched_product(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SearchedProduct::class, 'recent_search_id', 'id');
    }

    public function searched_user(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SearchedKeywordUser::class, 'recent_search_id', 'id');
    }
}

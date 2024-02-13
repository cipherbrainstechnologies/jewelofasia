<?php

namespace App\Model;

use App\User;
use App\VisitedProduct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchedKeywordUser extends Model
{
    protected $table = 'searched_keyword_users';

    protected $casts = [
        'recent_search_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = ['recent_search_id', 'user_id'];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function related_category(): HasMany
    {
        return $this->hasMany(CategorySearchedByUser::class, 'user_id', 'user_id');
    }

    public function related_product(): HasMany
    {
        return $this->hasMany(ProductSearchedByUser::class, 'user_id', 'user_id');
    }

    public function visited_products(): HasMany
    {
        return $this->hasMany(VisitedProduct::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class,'user_id', 'user_id');
    }

//    public function keyword()
//    {
//        return $this->belongsTo(RecentSearch::class, 'recent_search_id', 'id');
//    }

}

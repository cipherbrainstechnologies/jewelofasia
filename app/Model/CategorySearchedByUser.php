<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategorySearchedByUser extends Model
{
    protected $table = 'category_searched_by_user';

    protected $casts = [
        'category_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = ['category_id', 'user_id'];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

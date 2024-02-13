<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProductSearchedByUser extends Model
{
    protected $table = 'product_searched_by_user';

    protected $casts = [
        'product_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = ['product_id', 'user_id'];
}

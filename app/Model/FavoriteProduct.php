<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FavoriteProduct extends Model
{
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}

<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class VisitedProduct extends Model
{
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

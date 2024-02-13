<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductTag extends Pivot
{
    use HasFactory;

    protected $table = 'product_tag';

    protected $casts = [
        'id'=>'integer',
        'product_id'=>'integer',
        'tag_id'=>'integer'
    ];
}

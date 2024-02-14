<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCodes extends Model
{
    use HasFactory;

    protected $table = 'zipcodes';

    protected $fillable = ['city_id','zipcode','order_before_day','delivery_order_day','status'];

    public function translations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }

    public function city(){
        return $this->belongsTo('App\Models\City', 'city_id', 'id');        
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 'active');
    }

}

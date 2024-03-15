<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'plan_id', 'paypal_product_id', 'subscription_id', 'status'];
}

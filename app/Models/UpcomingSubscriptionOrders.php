<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingSubscriptionOrders extends Model
{
    use HasFactory;
    protected $table = "upcoming_subscription_order";
    protected $fillable = ['order_id','type','delivery_date','subscription_id','user_subscriptions_id'];
}

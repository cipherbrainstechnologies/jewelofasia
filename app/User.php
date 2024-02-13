<?php

namespace App;

use App\Model\CustomerAddress;
use App\Model\FavoriteProduct;
use App\Model\Order;
use App\Model\SearchedKeywordUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','f_name', 'l_name', 'phone', 'email', 'password', 'loyalty_point', 'wallet_balance', 'referral_code', 'referred_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_phone_verified' => 'integer',
        'loyalty_point' => 'float',
        'wallet_balance' => 'float',
    ];

    public function orders(){
        return $this->hasMany(Order::class,'user_id');
    }

    public function visited_products(): HasMany
    {
        return $this->hasMany(VisitedProduct::class, 'user_id', 'id');
    }

    public function addresses(){
        return $this->hasMany(CustomerAddress::class,'user_id');
    }

    public function favorite_products(){
        return $this->hasMany(FavoriteProduct::class,'user_id');
    }

    static function total_order_amount($customer_id)
    {
        $total_amount = 0;
        $customer = User::where(['id' => $customer_id])->first();
        foreach ($customer->orders as $order){
            $total_amount += $order->order_amount;
        }
        return $total_amount;
    }

    public function search_volume()
    {
        return $this->hasMany(SearchedKeywordUser::class, 'user_id', 'id');
    }
}

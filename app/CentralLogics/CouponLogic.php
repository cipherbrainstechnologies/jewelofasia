<?php
namespace App\CentralLogics;
use App\Model\Coupon;
use App\Model\Order;

class CouponLogic
{
    public static function coupon_apply($coupon_code, $order_amount, $customer_id, $delivery_charge)
    {
        $coupon = Coupon::active()->where(['code' => $coupon_code])->first();

        if (isset($coupon)) {

            //default coupon type
            if ($coupon['coupon_type'] == 'default') {
                $total = Order::where(['user_id' => $customer_id, 'coupon_code' => $coupon_code])->count();
                if ($total > $coupon['limit'] || $coupon['min_purchase'] > $order_amount) {
                    return 0;
                }

                if ($coupon['discount_type'] == 'percent') {
                    $coupon_discount = ($order_amount / 100) * $coupon['discount'];
                    if ($coupon['max_discount'] < $coupon_discount) {
                        $coupon_discount = $coupon['max_discount'];
                    }
                } else {
                    $coupon_discount = $coupon['discount'];
                }
                return $coupon_discount;

            }

            //first order coupon type
            if ($coupon['coupon_type'] == 'first_order') {
                $total = Order::where(['user_id' => $customer_id, 'coupon_code' => $coupon_code])->count();
                if ($total != 0 || $coupon['min_purchase'] > $order_amount) {
                    return 0;
                }

                if ($coupon['discount_type'] == 'percent') {
                    $coupon_discount = ($order_amount / 100) * $coupon['discount'];
                    if ($coupon['max_discount'] < $coupon_discount) {
                        $coupon_discount = $coupon['max_discount'];
                    }
                } else {
                    $coupon_discount = $coupon['discount'];
                }
                return $coupon_discount;
            }

            //free delivery
            if ($coupon['coupon_type'] == 'free_delivery') {
                $total = Order::where(['user_id' => $customer_id, 'coupon_code' => $coupon_code])->count();
                if ($total > $coupon['limit'] || $coupon['min_purchase'] > $order_amount) {
                    return 0;
                }

                return $coupon_discount = $delivery_charge;
            }

            //customer wise
            if ($coupon['coupon_type'] == 'customer_wise') {
                $total = Order::where(['user_id' => $customer_id, 'coupon_code' => $coupon_code])->count();
                if ($total > $coupon['limit'] || $coupon['min_purchase'] > $order_amount) {
                    return 0;
                }

                if ($coupon['customer_id'] != $customer_id) {
                    return 0;
                }

                if ($coupon['discount_type'] == 'percent') {
                    $coupon_discount = ($order_amount / 100) * $coupon['discount'];
                    if ($coupon['max_discount'] < $coupon_discount) {
                        $coupon_discount = $coupon['max_discount'];
                    }
                } else {
                    $coupon_discount = $coupon['discount'];
                }
                return $coupon_discount;
            }

        } else {
            return 0;
        }
    }
}

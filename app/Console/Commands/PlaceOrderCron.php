<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\userSubscription;
use Carbon\Carbon;
use App\Models\UpcomingSubscriptionOrders;
use App\Mail\Customer\OrderPlaced;
use Illuminate\Support\Facades\DB;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\User;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Log;

class PlaceOrderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PlaceOrderCron:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'place the subscription product order on order delivery day.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Order place cron Start execute");
        $order= [];
        $orderDetail = [];
        $today = Carbon::today()->startOfDay();
        $UpcomingOrders =UpcomingSubscriptionOrders::whereDate('delivery_date',$today)->get();

        if(!empty($UpcomingOrders)){
            foreach($UpcomingOrders as $UpcomingOrder){
                $subscriptionDetail = userSubscription::find($UpcomingOrder['user_subscriptions_id']);
                $customer = User::find($UpcomingOrder['user_id']);
                if(!empty($subscriptionDetail)){
                    if(!empty($subscriptionDetail['status'])){
                        $FetchOrderData = Order::find($UpcomingOrder['order_id'])->first();

                        if(!empty($FetchOrderData)){
                            try {
                                $order_id = 100000 + Order::all()->count() + 1;

                                $order = [
                                    'id' => $order_id,
                                    'user_id' => $FetchOrderData['user_id'],
                                    'is_guest' => $FetchOrderData['is_guest'],
                                    'order_amount' => $FetchOrderData['order_amount'],
                                    'coupon_code' =>  $FetchOrderData['coupon_code'] ?? null,
                                    //'coupon_discount_amount' => $coupon_discount_amount,
                                    'coupon_discount_amount' => $FetchOrderData['coupon_discount_amount'],
                                    'coupon_discount_title' => $FetchOrderData['coupon_discount_title'] ?? null,
                                    'payment_status' => $FetchOrderData['payment_status'],
                                    'order_status' => $FetchOrderData['order_status'],
                                    'payment_method' => $FetchOrderData['payment_method'],
                                    'transaction_reference' => $FetchOrderData['transaction_reference'],
                                    'order_note' => null,
                                    'order_type' => $FetchOrderData['order_type'],
                                    'branch_id' => $FetchOrderData['branch_id'],
                                    'delivery_address_id' => $FetchOrderData['delivery_address_id'],
                                    'time_slot_id' => $FetchOrderData['time_slot_id'],
                                    'delivery_date' => $UpcomingOrder['delivery_date'],
                                    'delivery_address' => $FetchOrderData['delivery_address'],
                                    'date' => date('Y-m-d'),
                                    'delivery_charge' => $FetchOrderData['delivery_charge'],
                                    'payment_by' => $FetchOrderData['payment_by'],
                                    'payment_note' => $FetchOrderData['payment_note'],
                                    'free_delivery_amount' => $FetchOrderData['free_delivery_amount'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                                
                                $orderProductDetails = OrderDetail::where('order_id',$UpcomingOrder['order_id'])->get();
                                if(!empty($orderProductDetails)){
                                    foreach($orderProductDetails as $orderProductDetail){
                                        $product = Product::find($orderProductDetail['product_id']);
                                        $orderDetail = [
                                            'order_id' => $order_id,
                                            'product_id' => $orderProductDetail['product_id'],
                                            'time_slot_id' => $orderProductDetail['time_slot_id'],
                                            'delivery_date' => $orderProductDetail['delivery_date'],
                                            'product_details' => $orderProductDetail['product_details'],
                                            'quantity' => $orderProductDetail['quantity'],
                                            'price' => $orderProductDetail['price'],
                                            'unit' => $orderProductDetail['unit'],
                                            'tax_amount' => $orderProductDetail['tax_amount'],
                                            'discount_on_product' => $orderProductDetail['discount_on_product'],
                                            'discount_type' => $orderProductDetail['discount_type'],
                                            'variant' => $orderProductDetail['variant'],
                                            'variation' => $orderProductDetail['variation'],
                                            'is_stock_decreased' => $orderProductDetail['is_stock_decreased'],
                                            'vat_status' => $orderProductDetail['vat_status'],
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ];
                                        $getType = json_decode($orderProductDetail['variation'],true);//[0]['type'];
                                        $type = $getType[0]['type'];
                                        $var_store = [];
                                        foreach (json_decode($product['variations'], true) as $var) {
                                            if ($type == $var['type']) {
                                                $var['stock'] -= $orderProductDetail['quantity'];
                                            }
                                            $var_store[] = $var;
                                        }
                                        Product::where(['id' => $product['id']])->update([
                                            'variations' => json_encode($var_store),
                                            'total_stock' => $product['total_stock'] - $orderProductDetail['quantity'],
                                            'popularity_count'=>$product['popularity_count']+1
                                        ]);
                                        DB::table('order_details')->insert($orderDetail);
                                    }
                                    DB::table('orders')->insertGetId($order);
                                    DB::commit();
                                    $order_status_message = ($FetchOrderData['payment_method'] == 'cash_on_delivery' || $FetchOrderData['payment_method'] == 'offline_payment') ? 'pending':'confirmed';

                                    //send email
                                    $emailServices = Helpers::get_business_settings('mail_config');

                                    if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($customer->email)) {
                                        Mail::to($customer->email)->send(new OrderPlaced($order_id));
                                    }
                                }
                            }catch (\Exception $e) {
                                DB::rollBack();
                                return response()->json([$e], 403);
                            }
                        }
                    }
                }
            }
        }
        Log::info("Order place cron End execute");
    }
}

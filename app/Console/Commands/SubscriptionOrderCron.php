<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\userSubscription;
use Carbon\Carbon;
use App\Models\UpcomingSubscriptionOrders;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use Illuminate\Support\Facades\Log;

class SubscriptionOrderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptionOrders:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'after end of day subscription product upcoming order date store into upcoming subscription orders table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        Log::info('Subscription order cron job start.');

        $today = Carbon::today()->startOfDay();
        $dateString = '2024-03-19';
        $date = Carbon::createFromFormat('Y-m-d', $dateString)->startOfDay();
        $userSubscription = userSubscription::where('status',1)->get();
        if(!empty($userSubscription)){
            $subscriptionIds = $userSubscription->pluck('subscription_id');
            $getSubscriptionOrder = order::with('details')->whereIn('transaction_reference',$subscriptionIds)->whereDate('delivery_date', $date)->get();
            if(!empty($getSubscriptionOrder)){
                if(!empty($getSubscriptionOrder)){
                    foreach($getSubscriptionOrder as $SuborderKey => $orderDetail){
                        if(!empty($orderDetail['details'])){
                            foreach($orderDetail['details'] as $orderDetailKey =>  $detail){
                               $productDetail = product::find($detail['product_id']);
                               $planDetail = userSubscription::where(['status'=> 1,'subscription_id' => $orderDetail->transaction_reference])->first();
                               //weekly
                               if($productDetail['paypal_weekly_plan_id'] == $planDetail['plan_id']){
                                    UpcomingSubscriptionOrders::create([
                                        "order_id"          => $orderDetail['id'],
                                        "type"              => 'weekly',
                                        "subscription_id"   => $planDetail['subscription_id'],
                                        "delivery_date"     => $date->copy()->addWeek()
                                    ]);
                               }
                               //bi-weekly
                               if($productDetail['paypal_biweekly_plan_id'] == $planDetail['plan_id']){
                                    UpcomingSubscriptionOrders::create([
                                        "order_id"      => $orderDetail['id'],
                                        "type"          => 'bi-weekly',
                                        "subscription_id"   => $planDetail['subscription_id'],
                                        "delivery_date" => $date->copy()->addWeek(2)
                                    ]);
                               }
                               //monthly
                               if($productDetail['paypal_monthly_plan_id'] == $planDetail['plan_id']){
                                    UpcomingSubscriptionOrders::create([
                                        "order_id"      => $orderDetail['id'],
                                        "type"          => 'monthly',
                                        "subscription_id"   => $planDetail['subscription_id'],
                                        "delivery_date" => $date->copy()->addMonth()
                                    ]);
                               }
                            }
                        }
                    }
                }
            }
        }
        Log::info('Subscription order cron job end.');
    }
}

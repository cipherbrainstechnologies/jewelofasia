<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\PaypalHelper;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Conversation;
use App\Model\Newsletter;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\userSubscription;
use Carbon\Carbon;
use App\Models\UpcomingSubscriptionOrders;
use App\Mail\Customer\OrderPlaced;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    
    public function __construct(
        private User $user,
        private Order $order,
        private Newsletter $newsletter,
        private BusinessSetting $business_setting,
        private Conversation $conversation,
        private PaypalHelper $paypalHelper,
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function customer_list(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $customers = $this->user->with(['orders'])->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%")
                                ->orWhere('l_name', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%");
                        }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $customers = $this->user->with(['orders']);
        }
        $customers = $customers->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.customer.list', compact('customers','search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $customers=$this->user->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('admin-views.customer.partials._table',compact('customers'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function view(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        $customer = $this->user->find($id);
        if (isset($customer)) {
            $query_param = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_amount', 'like', "%{$value}%");
                        }
                });
                $query_param = ['search' => $request['search']];
            }else{
                $orders = $this->order->where(['user_id' => $id]);
            }
            $orders = $orders->latest()->paginate(Helpers::getPagination())->appends($query_param);
            $subscriptions = userSubscription::where('user_id', $id)->latest()->paginate(Helpers::getPagination())->appends($query_param);
            foreach($subscriptions as $subscription) {
                $product = Product::select('id', 'paypal_product_id', 'paypal_weekly_plan_id', 'paypal_biweekly_plan_id', 'paypal_monthly_plan_id')->where('paypal_product_id', $subscription->paypal_product_id)->first();
                $plan = '';
                if($subscription->plan_id === $product->paypal_weekly_plan_id) {
                    $plan = 'weekly';
                }
                if($subscription->plan_id === $product->paypal_biweekly_plan_id) {
                    $plan = 'bi-weekly';
                }
                if($subscription->plan_id === $product->paypal_monthly_plan_id) {
                    $plan = 'monthly';
                }
                $subscription->plan = $plan;
            } 
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search', 'subscriptions'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function subscribed_emails(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = $this->newsletter->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $newsletters = $this->newsletter;
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $customer = $this->user->find($request->id);
        //return $customer;
        if (Storage::disk('public')->exists('customer/' . $customer['image'])) {
            Storage::disk('public')->delete('customer/' . $customer['image']);
        }

        $conversations = $this->conversation->where('user_id', $request->id)->get();
        foreach ($conversations as $conversation){
            if ($conversation->checked == 0){
                $conversation->checked = 1;
                $conversation->save();
            }
        }

        $customer->delete();

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($customer['email'])) {
                $name = $customer->f_name. ' '. $customer->l_name;
                Mail::to($customer->email)->send(new \App\Mail\Customer\CustomerDelete($name));
            }
        } catch (\Exception $e) {
        }

        Toastr::success(translate('Customer removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $user = $this->user->find($request->id);
        $user->is_block = $request->status;
        $user->save();

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($user['email'])) {
                Mail::to($user->email)->send(new \App\Mail\Customer\CustomerChangeStatus($user));
            }
        } catch (\Exception $e) {
        }

        Toastr::success(translate('Block status updated!'));
        return back();
    }


    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export_customer(Request $request): StreamedResponse|string
    {
        $storage = [];
        $query_param = [];
        $search = $request['search'];

        $customers = $this->user->when($request->has('search'), function ($query) use ($request) {
                $key = explode(' ', $request['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
                $query_param = ['search' => $request['search']];
            })
            ->get();

        foreach($customers as $customer){

            $storage[] = [
                'first_name' => $customer['f_name'],
                'last_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ];
        }
        return (new FastExcel($storage))->download('customers.xlsx');
    }

    public function cancel_subscription($id): RedirectResponse
    {
        $this->paypalHelper->cancel_subscription($id);
        $user_sub = userSubscription::select('id')->where('subscription_id', $id)->first();
        $subscription = userSubscription::find($user_sub->id);
        $subscription->status = 0;
        $subscription->save();
        Toastr::success(translate('Subscription Cancelled!'));
        return back();
    }

    public function mmm(){
        // $today = Carbon::today();
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
                                        "order_id"              => $orderDetail['id'],
                                        "type"                  => 'weekly',
                                        "subscription_id"       => $planDetail['subscription_id'],
                                        "user_subscriptions_id" => $planDetail['id'],
                                        "delivery_date"         => $date->copy()->addWeek()
                                    ]);
                               }
                               //bi-weekly
                               if($productDetail['paypal_biweekly_plan_id'] == $planDetail['plan_id']){
                                    UpcomingSubscriptionOrders::create([
                                        "order_id"      => $orderDetail['id'],
                                        "type"          => 'bi-weekly',
                                        "subscription_id"   => $planDetail['subscription_id'],
                                        "user_subscriptions_id" => $planDetail['id'],
                                        "delivery_date" => $date->copy()->addWeek(2)
                                    ]);
                               }
                               //monthly
                               if($productDetail['paypal_monthly_plan_id'] == $planDetail['plan_id']){
                                    UpcomingSubscriptionOrders::create([
                                        "order_id"      => $orderDetail['id'],
                                        "type"          => 'monthly',
                                        "subscription_id"   => $planDetail['subscription_id'],
                                        "user_subscriptions_id" => $planDetail['id'],
                                        "delivery_date" => $date->copy()->addMonth()
                                    ]);
                               }
                            }
                        }
                    }
                }
            }
        }
    }

    public function mmm1(){
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
    }
}

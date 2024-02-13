<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Mail\Deliveryman\DMDelete;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Models\OfflinePayment;
use App\Models\OrderPartialPayment;
use App\Traits\HelperTrait;
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
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use HelperTrait;
    public function __construct(
        private Branch $branch,
        private BusinessSetting $business_setting,
        private DeliveryMan $delivery_man,
        private Order $order,
        private OrderDetail $order_detail,
        private Product $product,
        private User $user
    ){}

    /**
     * @param Request $request
     * @param $status
     * @return Factory|View|Application
     */
    public function list(Request $request, $status): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $query_param = [];
        $search = $request['search'];

        $branches = $this->branch->all();
        $branch_id = $request['branch_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        if ($status != 'all') {
            $query = $this->order->with(['customer', 'branch'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->where(['order_status' => $status]);

        } else {
            $query = $this->order->with(['customer', 'branch'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                });
        }

        $query_param = ['branch_id' => $branch_id, 'start_date' => $start_date,'end_date' => $end_date ];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%");
                }
            });
            $query_param = ['search' => $request['search'], 'branch_id' => $request['branch_id'], 'start_date' => $request['start_date'],'end_date' => $request['end_date'] ];
        }

        $orders = $query->notPos()->orderBy('id', 'desc')->paginate(Helpers::getPagination())->appends($query_param);

        $count_data = [
            'pending' => $this->order->notPos()->where(['order_status'=>'pending'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'confirmed' => $this->order->notPos()->where(['order_status'=>'confirmed'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'processing' => $this->order->notPos()->where(['order_status'=>'processing'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'out_for_delivery' => $this->order->notPos()->where(['order_status'=>'out_for_delivery'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'delivered' => $this->order->notPos()->where(['order_status'=>'delivered'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'canceled' => $this->order->notPos()->where(['order_status'=>'canceled'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'returned' => $this->order->notPos()->where(['order_status'=>'returned'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),

            'failed' => $this->order->notPos()->where(['order_status'=>'failed'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->count(),
        ];

        return view('admin-views.order.list', compact('orders', 'status', 'search', 'branches', 'branch_id', 'start_date', 'end_date', 'count_data'));
    }

    /**
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function details($id): Factory|View|Application|RedirectResponse
    {
        $order = $this->order->with(['details', 'offline_payment'])->where(['id' => $id])->first();
        $delivery_man = $this->delivery_man->where(['is_active'=>1])
            ->where(function($query) use ($order) {
                $query->where('branch_id', $order->branch_id)
                    ->orWhere('branch_id', 0);
            })
            ->get();

        if (isset($order)) {
            return view('admin-views.order.order-view', compact('order', 'delivery_man'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {

        $key = explode(' ', $request['search']);
        $orders = $this->order->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->latest()->paginate(2);

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function date_search(Request $request): \Illuminate\Http\JsonResponse
    {
        $dateData = ($request['dateData']);

        $orders = $this->order->where(['delivery_date' => $dateData])->latest()->paginate(10);
        // $timeSlots = $orders->pluck('time_slot_id')->unique()->toArray();
        // if ($timeSlots) {

        //     $timeSlots = TimeSlot::whereIn('id', $timeSlots)->get();
        // } else {
        //     $timeSlots = TimeSlot::orderBy('id')->get();

        // }
        // dd($orders);

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render(),
            // 'timeSlot' => $timeSlots
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function time_search(Request $request): \Illuminate\Http\JsonResponse
    {

        $orders = $this->order->where(['time_slot_id' => $request['timeData']])->where(['delivery_date' => $request['dateData']])->get();
        // dd($orders)->toArray();

        return response()->json([
            'view' => view('admin-views.order.partials._table', compact('orders'))->render(),
        ]);

    }

    private function calculate_refund_amount($order, $amount){
        $customer = $this->user->find($order['user_id']);
        $wallet = CustomerLogic::create_wallet_transaction($customer->id, $amount, 'refund', $order['id']);
        if ($wallet){
            $customer->wallet_balance += $amount;
        }
        $customer->save();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled'])) {
            Toastr::warning(translate('you_can_not_change_the_status_of '. $order->order_status .' order'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') {
            Toastr::warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'],['cash_on_delivery','wallet_payment', 'offline_payment'])) {
            Toastr::warning(translate('add_your_payment_reference_first'));
            return back();
        }

        if ( ($request->order_status == 'out_for_delivery' || $request->order_status == 'delivered') && $order['delivery_man_id'] == null && $order['order_type'] != 'self_pickup') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }

        //refund amount to wallet
        if (in_array($request['order_status'] , ['returned', 'failed', 'canceled']) && $order['is_guest'] == 0 && isset($order->customer) && Helpers::get_business_settings('wallet_status') == 1) {

            // wallet order
            if ($order['payment_method'] == 'wallet_payment' && $order->partial_payment->isEmpty() ){
                $this->calculate_refund_amount(order: $order, amount: $order->order_amount);
            }

            // digital order
            if ($order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] != 'wallet_payment' && $order['payment_method'] != 'offline_payment' && $order->partial_payment->isEmpty()){
                $this->calculate_refund_amount(order: $order, amount: $order->order_amount);
            }

            //offline order
            if ($order['payment_method'] == 'offline_payment' && $order['payment_status'] == 'paid' && $order->partial_payment->isEmpty()){
                $this->calculate_refund_amount(order: $order, amount: $order['order_amount']);
            }

            //partial payment
            if ($order->partial_payment->isNotEmpty()){
                $partial_payment_total = $order->partial_payment->sum('paid_amount');
                $this->calculate_refund_amount(order: $order, amount: $partial_payment_total);
            }

        }

        //stock adjust
        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            foreach ($order->details as $detail) {

                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);

                    if($product != null){
                        $type = json_decode($detail['variation'])[0]->type;
                        $var_store = [];
                        foreach (json_decode($product['variations'], true) as $var) {
                            if ($type == $var['type']) {
                                $var['stock'] += $detail['quantity'];
                            }
                            $var_store[] = $var;
                        }
                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($var_store),
                            'total_stock' => $product['total_stock'] + $detail['quantity'],
                        ]);
                        $this->order_detail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 0,
                        ]);
                    }
                }else{
                    //Toastr::warning(translate('Product_deleted'));
                }

            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = $this->product->find($detail['product_id']);
                    if($product != null){
                        //check stock
                        foreach ($order->details as $c) {
                            $product = $this->product->find($c['product_id']);
                            $type = json_decode($c['variation'])[0]->type;
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                                    Toastr::error(translate('Stock is insufficient!'));
                                    return back();
                                }
                            }
                        }

                        $type = json_decode($detail['variation'])[0]->type;
                        $var_store = [];
                        foreach (json_decode($product['variations'], true) as $var) {
                            if ($type == $var['type']) {
                                $var['stock'] -= $detail['quantity'];
                            }
                            $var_store[] = $var;
                        }
                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($var_store),
                            'total_stock' => $product['total_stock'] - $detail['quantity'],
                        ]);
                        $this->order_detail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 1,
                        ]);
                    }
                    else{

                        //Toastr::warning(translate('Product_deleted'));
                    }

                }
            }
        }

        if ($request->order_status == 'delivered') {
            if ($order->is_guest == 0){
                if($order->user_id) {
                    CustomerLogic::create_loyalty_point_transaction($order->user_id, $order->id, $order->order_amount, 'order_place');
                }

                $user = $this->user->find($order->user_id);
                $is_first_order = $this->order->where(['user_id' => $user->id, 'order_status' => 'delivered'])->count('id');
                $referred_by_user = $this->user->find($user->referred_by);

                if ($is_first_order < 2 && isset($user->referred_by) && isset($referred_by_user)){
                    if($this->business_setting->where('key','ref_earning_status')->first()->value == 1) {
                        CustomerLogic::referral_earning_wallet_transaction($order->user_id, 'referral_order_place', $referred_by_user->id);
                    }
                }
            }

            //partials payment transaction
            if ($order['payment_method'] == 'cash_on_delivery'){
                $partial_data = OrderPartialPayment::where(['order_id' => $order->id])->first();
                if ($partial_data){
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $order['id'];
                    $partial->paid_with = 'cash_on_delivery';
                    $partial->paid_amount = $partial_data->due_amount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }
        }

        $order->order_status = $request->order_status;
        $order->save();

        $message = Helpers::order_status_update_message($request->order_status);
        $language_code = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
        $fcm_token = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

        if ($language_code != 'en'){
            $message = $this->translate_message($language_code, $request->order_status);
        }
        $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for Customer!'));
        }

        //delivery man notification
        if ($request->order_status == 'processing' && $order->delivery_man != null) {
            $fcm_token = $order->delivery_man->fcm_token;
            $message = Helpers::order_status_update_message('deliveryman_order_processing');
            $dm_language_code = $order->delivery_man->language_code ?? 'en';

            if ($dm_language_code != 'en'){
                $message = $this->translate_message($dm_language_code, 'deliveryman_order_processing');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    /**
     * @param $order_id
     * @param $delivery_man_id
     * @return JsonResponse
     */
    public function add_delivery_man($order_id, $delivery_man_id): \Illuminate\Http\JsonResponse
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }

        $order = $this->order->find($order_id);

        if ($order->order_status == 'pending' || $order->order_status == 'confirmed' || $order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false], 200);
        }

        $order->delivery_man_id = $delivery_man_id;
        $order->save();

        $dm_message = Helpers::order_status_update_message('del_assign');
        $dm_language_code = $order->delivery_man ? $order->delivery_man->language_code : 'en';
        $dm_fcm_token = $order->delivery_man ? $order->delivery_man->fcm_token : null;

        if ($dm_language_code != 'en'){
            $dm_message = $this->translate_message($dm_language_code, 'del_assign');
        }
        $value = $this->dynamic_key_replaced_message(message: $dm_message, type: 'order', order: $order);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order'
                ];
                Helpers::send_push_notif_to_device($dm_fcm_token, $data);

                $customer_notify_message = Helpers::order_status_update_message('customer_notify_message');
                $customer_language_code = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
                $customer_fcm_token = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

                if ($customer_language_code != 'en'){
                    $customer_notify_message = $this->translate_message($customer_language_code, 'customer_notify_message');
                }
                $value = $this->dynamic_key_replaced_message(message: $customer_notify_message, type: 'order', order: $order);

                if($customer_notify_message) {

                    $data['description'] = $value;
                    Helpers::send_push_notif_to_device($customer_fcm_token, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CentralLogics\translate('Push notification failed for DeliveryMan!'));
        }

        Toastr::success('Deliveryman successfully assigned/changed!');
        return response()->json(['status' => true], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function payment_status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->find($request->id);

        if ($order->payment_method == 'offline_payment' && isset($order->offline_payment) && $order->offline_payment?->status != 1){
            Toastr::warning(translate('please_verify_your_offline_payment_verification'));
            return back();
        }

        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning(translate('Add your payment reference code first!'));
            return back();
        }

        if ($request->payment_status == 'paid' && $order['order_status'] == 'pending'){
            $order->order_status = 'confirmed';

            $message = Helpers::order_status_update_message('confirmed');
            $language_code = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
            $fcm_token = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

            if ($language_code != 'en'){
                $message = $this->translate_message($language_code, 'confirmed');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
               // Toastr::warning(\App\CentralLogics\translate('Push notification failed for Customer!'));
            }

        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update_shipping(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Delivery Information updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function update_time_slot(Request $request)
    {
        if ($request->ajax()) {
            $order = $this->order->find($request->id);
            $order->time_slot_id = $request->timeSlot;
            $order->save();
            $data = $request->timeSlot;

            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function update_deliveryDate(Request $request)
    {
        if ($request->ajax()) {
            $order = $this->order->find($request->id);
            $order->delivery_date = $request->deliveryDate;
           // dd($order);
            $order->save();
            $data = $request->deliveryDate;
            return response()->json($data);
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function generate_invoice($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $order = $this->order->where('id', $id)->first();
        $footer_text = $this->business_setting->where(['key' => 'footer_text'])->first();
        return view('admin-views.order.invoice', compact('order', 'footer_text'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function add_payment_ref_code(Request $request, $id)
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference'],
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function branch_filter($id): \Illuminate\Http\RedirectResponse
    {
        session()->put('branch_filter', $id);
        return back();
    }

    /**
     * @param Request $request
     * @param $status
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export_orders(Request $request, $status): StreamedResponse|string
    {
        $query_param = [];
        $search = $request['search'];
        $branch_id = $request['branch_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        if ($status != 'all') {
            $query = $this->order->with(['customer', 'branch'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->where(['order_status' => $status]);
        } else {
            $query = $this->order->with(['customer', 'branch'])
                ->when((!is_null($branch_id) && $branch_id != 'all'), function ($query) use ($branch_id) {
                    return $query->where('branch_id', $branch_id);
                })->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                });
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        //$orders = $query->notPos()->orderBy('id', 'desc')->paginate(Helpers::getPagination())->appends($query_param);
        $orders = $query->notPos()->orderBy('id', 'desc')->get();

        $storage = [];

        foreach($orders as $order){
            $branch = $order->branch ? $order->branch->name : '';
            $customer = $order->customer ? $order->customer->f_name .' '. $order->customer->l_name : 'Customer Deleted';
            //$delivery_address = $order->delivery_address ? $order->delivery_address['address'] : '';
            $delivery_man = $order->delivery_man ? $order->delivery_man->f_name .' '. $order->delivery_man->l_name : '';
            $timeslot = $order->time_slot ? $order->time_slot->start_time .' - '. $order->time_slot->end_time : '';

            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $order['order_amount'],
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount'=>$order['total_tax_amount'],
                'payment_method' => $order['payment_method'],
                'transaction_reference' => $order['transaction_reference'],
               // 'delivery_address' => $delivery_address,
                'delivery_man' => $delivery_man,
                'delivery_charge' => $order['delivery_charge'],
                'coupon_code' => $order['coupon_code'],
                'order_type' => $order['order_type'],
                'branch'=>  $branch,
                'time_slot_id' => $timeslot,
                'date' => $order['date'],
                'delivery_date' => $order['delivery_date'],
                'extra_discount' => $order['extra_discount'],
            ];
        }
        //return $storage;
        return (new FastExcel($storage))->download('orders.xlsx');
    }

    /**
     * @param $order_id
     * @param $status
     * @return JsonResponse
     */
    public function verify_offline_payment($order_id, $status): JsonResponse
    {
        $offline_data = OfflinePayment::where(['order_id' => $order_id])->first();
        if (!isset($offline_data)){
            return response()->json(['status' => false], 200);
        }
        $offline_data->status = $status;
        $offline_data->save();

        $order = Order::find($order_id);
        if (!isset($order)){
            return response()->json(['status' => false], 200);
        }

        if ($offline_data->status == 1){
            $order->order_status = 'confirmed';
            $order->payment_status = 'paid';
            $order->save();

            $message = Helpers::order_status_update_message('confirmed');
            $language_code = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : ($order->guest ? $order->guest->language_code : 'en');
            $fcm_token = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

            if ($language_code != 'en'){
                $message = $this->translate_message($language_code, 'confirmed');
            }
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
                //
            }

        }elseif ($offline_data->status == 2){
            $fcm_token = null;
            if($order->is_guest == 0){
                $fcm_token = $order->customer ? $order->customer->cm_firebase_token : null;
            }elseif($order->is_guest == 1){
                $fcm_token = $order->guest ? $order->guest->fcm_token : null;
            }
            if ($fcm_token != null) {
                try {
                    $data = [
                        'title' => translate('Order'),
                        'description' => translate('Offline payment is not verified'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                } catch (\Exception $e) {
                }
            }
        }
        return response()->json(['status' => true], 200);
    }
}

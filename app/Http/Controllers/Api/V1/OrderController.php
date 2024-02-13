<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\CouponLogic;
use App\Http\Controllers\Controller;
use App\Mail\Customer\OrderPlaced;
use App\Model\Coupon;
use App\Model\CustomerAddress;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Models\GuestUser;
use App\Models\OfflinePayment;
use App\Models\OrderPartialPayment;
use App\Traits\HelperTrait;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use HelperTrait;
    public function __construct(
        private Coupon $coupon,
        private CustomerAddress $customer_address,
        private DMReview $dm_review,
        private Order $order,
        private OrderDetail $order_detail,
        private Product $product,
        private Review $review,
        private User $user
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function track_order(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request->input('phone');
        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        $order = $this->order->find($request['order_id']);

        if (!isset($order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if (!is_null($phone)){
            if ($order['is_guest'] == 0){
                $track_order = $this->order
                    ->with(['customer', 'delivery_address'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->first();
            }else{
                $track_order = $this->order
                    ->with(['delivery_address'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('delivery_address', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->first();
            }
        }else{
            $track_order = $this->order
                ->where(['id' => $request['order_id'], 'user_id' => $user_id, 'is_guest' => $user_type])
                ->first();
        }

        if (!isset($track_order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function place_order(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_amount' => 'required',
            'payment_method'=>'required',
            'delivery_address_id' => 'required',
            'order_type' => 'required|in:self_pickup,delivery',
            'branch_id' => 'required',
            'distance' => 'required',
            'is_partial' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (count($request['cart']) <1) {
            return response()->json(['errors' => [['code' => 'empty-cart', 'message' => translate('cart is empty')]]], 403);
        }

        $min_amount = Helpers::get_business_settings('minimum_order_value');
        if ($min_amount > $request['order_amount']){
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Order amount must be equal or more than '. $min_amount];
            return response()->json(['errors' => $errors], 401);
        }

        $max_amount = Helpers::get_business_settings('maximum_amount_for_cod_order');
        if ($request->payment_method == 'cash_on_delivery' && Helpers::get_business_settings('maximum_amount_for_cod_order_status') == 1 && ($max_amount < $request['order_amount'])){
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'For Cash on Delivery, order amount must be equal or less than '. $max_amount];
            return response()->json(['errors' => $errors], 401);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        if(auth('api')->user()){
            $customer = $this->user->find(auth('api')->user()->id);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        //wallet payment validation
        if($request->payment_method == 'wallet_payment' && Helpers::get_business_settings('wallet_status', false) != 1)
        {
            return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('customer_wallet_status_is_disable')]]], 403);
        }

        if($request->payment_method == 'wallet_payment' && $customer->wallet_balance < $request['order_amount'])
        {
            return response()->json([
                'errors' => [['code' => 'payment_method', 'message' => translate('you_do_not_have_sufficient_balance_in_wallet')]]], 403);
        }

        //partial order validation
        if ($request['is_partial'] == 1) {
            if (Helpers::get_business_settings('wallet_status') != 1){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('customer_wallet_status_is_disable')]]], 403);
            }
            if (isset($customer) && $customer->wallet_balance > $request['order_amount']){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('since your wallet balance is more than order amount, you can not place partial order')]]], 403);
            }
            if (isset($customer) && $customer->wallet_balance < 1){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('since your wallet balance is less than 1, you can not place partial order')]]], 403);
            }
        }

        foreach ($request['cart'] as $c) {
            $product = $this->product->find($c['product_id']);
            $type = $c['variation'][0]['type'];
            foreach (json_decode($product['variations'], true) as $var) {
                if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                    $validator->getMessageBag()->add('stock', 'Stock is insufficient! available stock ' . $var['stock']);
                }
            }
        }
        $free_delivery_amount = 0;
        if ($request['order_type'] == 'self_pickup'){
            $delivery_charge = 0;
        } elseif (Helpers::get_business_settings('free_delivery_over_amount_status') == 1 && (Helpers::get_business_settings('free_delivery_over_amount') <= $request['order_amount'])){
            $delivery_charge = 0;
            $free_delivery_amount = Helpers::get_delivery_charge($request['distance']);
        } else{
            $delivery_charge = Helpers::get_delivery_charge($request['distance']);
        }

        $coupon = $this->coupon->active()->where(['code' => $request['coupon_code']])->first();

        if (isset($coupon)) {
            if ($coupon['coupon_type'] == 'free_delivery') {
                $free_delivery_amount = Helpers::get_delivery_charge($request['distance']);
                $coupon_discount = 0;
                $delivery_charge = 0;
            } else {
                $coupon_discount = $request['coupon_discount_amount'];
            }
        }else{
            $coupon_discount = $request['coupon_discount_amount'];
        }

        if ($request['is_partial'] == 1) {
            $payment_status = ($request->payment_method == 'cash_on_delivery' || $request->payment_method == 'offline_payment') ? 'partially_paid' : 'paid';

        } else {
            $payment_status = ($request->payment_method == 'cash_on_delivery' || $request->payment_method == 'offline_payment') ? 'unpaid' : 'paid';
        }

        $order_status = ($request->payment_method == 'cash_on_delivery' || $request->payment_method == 'offline_payment') ? 'pending' : 'confirmed';

        try {
            DB::beginTransaction();
            $order_id = 100000 + Order::all()->count() + 1;
            $or = [
                'id' => $order_id,
                'user_id' => $user_id,
                'is_guest' => $user_type,
                'order_amount' => $request['order_amount'],
                'coupon_code' =>  $request['coupon_code'],
                //'coupon_discount_amount' => $coupon_discount_amount,
                'coupon_discount_amount' => $coupon_discount,
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'payment_status' => $payment_status,
                'order_status' => $order_status,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference ?? null,
                'order_note' => $request['order_note'],
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'delivery_address_id' => $request->delivery_address_id,
                'time_slot_id' => $request->time_slot_id,
                'delivery_date' => $request->delivery_date,
                'delivery_address' => json_encode(CustomerAddress::find($request->delivery_address_id) ?? null),
                'date' => date('Y-m-d'),
                'delivery_charge' => $delivery_charge,
                'payment_by' => $request['payment_method'] == 'offline_payment' ? $request['payment_by'] : null,
                'payment_note' => $request['payment_method'] == 'offline_payment' ? $request['payment_note'] : null,
                'free_delivery_amount' => $free_delivery_amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $o_time = $or['time_slot_id'];
            $o_delivery = $or['delivery_date'];

            $total_tax_amount = 0;


            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);

                if ($product['maximum_order_quantity'] < $c['quantity']){
                    return response()->json(['errors' => $product['name']. ' '. translate('quantity_must_be_equal_or_less_than '. $product['maximum_order_quantity'])], 401);
                }

                if (count(json_decode($product['variations'], true)) > 0) {
                    $price = Helpers::variation_price($product, json_encode($c['variation']));
                } else {
                    $price = $product['price'];
                }

                $tax_on_product = Helpers::tax_calculate($product, $price);

                $category_id = null;
                foreach (json_decode($product['category_ids'], true) as $cat) {
                    if ($cat['position'] == 1){
                        $category_id = ($cat['id']);
                    }
                }

                $category_discount = Helpers::category_discount_calculate($category_id, $price);
                $product_discount = Helpers::discount_calculate($product, $price);
                if ($category_discount >= $price){
                    $discount = $product_discount;
                    $discount_type = 'discount_on_product';
                }else{
                    $discount = max($category_discount, $product_discount);
                    $discount_type = $product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category';
                }

                $or_d = [
                    'order_id' => $order_id,
                    'product_id' => $c['product_id'],
                    'time_slot_id' => $o_time,
                    'delivery_date' => $o_delivery,
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'unit' => $product['unit'],
                    'tax_amount' => $tax_on_product,
                    'discount_on_product' => $discount,
                    'discount_type' => $discount_type,
                    'variant' => json_encode($c['variant']),
                    'variation' => json_encode($c['variation']),
                    'is_stock_decreased' => 1,
                    'vat_status' => Helpers::get_business_settings('product_vat_tax_status') === 'included' ? 'included' : 'excluded',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $total_tax_amount += $or_d['tax_amount'] * $c['quantity'];

                $type = $c['variation'][0]['type'];
                $var_store = [];
                foreach (json_decode($product['variations'], true) as $var) {
                    if ($type == $var['type']) {
                        $var['stock'] -= $c['quantity'];
                    }
                    $var_store[] = $var;
                }

                $this->product->where(['id' => $product['id']])->update([
                    'variations' => json_encode($var_store),
                    'total_stock' => $product['total_stock'] - $c['quantity'],
                    'popularity_count'=>$product['popularity_count']+1
                ]);

                DB::table('order_details')->insert($or_d);
            }
            $or['total_tax_amount'] = $total_tax_amount;
            DB::table('orders')->insertGetId($or);

            if($request->payment_method == 'wallet_payment'){
                $amount = $or['order_amount'];
                CustomerLogic::create_wallet_transaction($or['user_id'], $amount, 'order_place', $or['id']);
            }

            if ($request->payment_method == 'offline_payment') {
                $offline_payment = new OfflinePayment();
                $offline_payment->order_id = $or['id'];
                $offline_payment->payment_info = json_encode($request['payment_info']);
                $offline_payment->save();
            }

            if ($request['is_partial'] == 1){
                $total_order_amount = $or['order_amount'];
                $wallet_amount = $customer->wallet_balance;
                $due_amount = $total_order_amount - $wallet_amount;

                $wallet_transaction = CustomerLogic::create_wallet_transaction($or['user_id'], $wallet_amount, 'order_place', $or['id']);

                $partial = new OrderPartialPayment();
                $partial->order_id = $or['id'];
                $partial->paid_with = 'wallet_payment';
                $partial->paid_amount = $wallet_amount;
                $partial->due_amount = $due_amount;
                $partial->save();

                if ($request['payment_method'] != 'cash_on_delivery'){
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $or['id'];
                    $partial->paid_with = $request['payment_method'];
                    $partial->paid_amount = $due_amount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }

            DB::commit();

            //push notification
            if ((bool)auth('api')->user()){
                $fcm_token = auth('api')->user()->cm_firebase_token;
                $language_code = auth('api')->user()->language_code ?? 'en';
            }else{
                $guest = GuestUser::find($request->header('guest-id'));
                $fcm_token = $guest ? $guest->fcm_token : '';
                $language_code = $guest ? $guest->language_code : 'en';
            }

            $order_status_message = ($request->payment_method == 'cash_on_delivery' || $request->payment_method == 'offline_payment') ? 'pending':'confirmed';
            $message = Helpers::order_status_update_message($order_status_message);

            if ($language_code != 'en'){
                $message = $this->translate_message($language_code, $order_status_message);
            }

            $order = $this->order->find($order_id);
            $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

            try {
                if ($value) {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order_id,
                        'image' => '',
                        'type' => 'order'
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }

                //send email
                $emailServices = Helpers::get_business_settings('mail_config');

                if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($customer->email)) {
                    Mail::to($customer->email)->send(new OrderPlaced($order_id));
                }

            } catch (\Exception $e) {
            }

            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $order_id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([$e], 403);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_list(Request $request): \Illuminate\Http\JsonResponse
    {
        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

//        $orders = $this->order->with(['customer', 'delivery_man.rating',  'details' => function ($query) {
//            $query->select('order_id', DB::raw('sum(quantity) as total_quantity'))
//                ->groupBy('order_id');
//        }])
//            //->withCount('details')
//            ->withSum('details:quantity as total_quantity_all')
//            ->where(['user_id' => $user_id, 'is_guest' => $user_type])->get();

        $orders = $this->order->with(['customer', 'delivery_man.rating', 'details:id,order_id,quantity'])
            ->where(['user_id' => $user_id, 'is_guest' => $user_type])
            ->get();

        $orders->each(function ($order) {
            $order->total_quantity = $order->details->sum('quantity');
        });

        $orders->map(function ($data) {
            $data['deliveryman_review_count'] = $this->dm_review->where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();
            return $data;
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (integer)$data->details_count;
            return $data;
        }), 200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request->input('phone');
        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;


        $order = $this->order->find($request['order_id']);
        if (!isset($order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if (!is_null($phone)){
            if ($order['is_guest'] == 0){
                $details = $this->order_detail
                    ->with(['order', 'order.delivery_address' ,'order.customer', 'order.partial_payment'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->get();
            }else{
                $details = $this->order_detail
                    ->with(['order', 'order.delivery_address', 'order.partial_payment'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.delivery_address', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->get();
            }
        }else{
            $details = $this->order_detail
                ->with(['order', 'order.partial_payment'])
                ->where(['order_id' => $request['order_id']])
                ->whereHas('order', function ($q) use ($user_id, $user_type){
                    $q->where(['user_id' => $user_id, 'is_guest' => $user_type]);
                })
                ->orderBy('id', 'desc')
                ->get();
        }


        if ($details->count() > 0) {
            foreach ($details as $det) {
               /* $det['variation'] = json_decode($det['variation'], true);
                if ($this->order->find($request->order_id)->order_type == 'pos') {
                    $det['variation'] = (string)implode('-', array_values($det['variation'])) ?? null;
                }
                else {
                    $det['variation'] = (string)$det['variation'][0]['type']??null;
                }*/

                $variation = json_decode($det['variation'], true);

                if (empty($variation)) {
                    $det['variation'] = null; // Handle cases where variation is empty
                } else {
                    if ($this->order->find($request->order_id)->order_type == 'pos') {
                        // Handle variations in the format [{"type":"Mid"}]
                        $det['variation'] = (string)implode('-', array_values($variation));
                    } else {
                        // Handle variations in the format {"Size":"Mid"}
                        $det['variation'] = (string)($variation[0]['type'] ?? null);
                    }
                }

                $det['review_count'] = $this->review->where(['order_id' => $det['order_id'], 'product_id' => $det['product_id']])->count();
                $product = $this->product->where('id', $det['product_id'])->first();
                $det['product_details'] = isset($product) ? Helpers::product_data_formatting($product) : null;
                //$det['product_details'] = Helpers::product_data_formatting($det['product_details']);
            }
            return response()->json($details, 200);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => 'Order not found!']
                ]
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancel_order(Request $request): \Illuminate\Http\JsonResponse
    {
        $order = $this->order::find($request['order_id']);

        if (!isset($order)){
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if ($order->order_status != 'pending'){
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order can only cancel when order status is pending!']]], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $user_id, 'is_guest' => $user_type, 'id' => $request['order_id']])->first()) {

            $order = $this->order->with(['details'])->where(['user_id' => $user_id, 'is_guest' => $user_type, 'id' => $request['order_id']])->first();

            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);
                    if (isset($product)){
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
                }
            }
            $this->order->where(['user_id' => $user_id, 'is_guest' => $user_type, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled',
            ]);
            return response()->json(['message' => 'Order canceled'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!'],
            ],
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_payment_method(Request $request): \Illuminate\Http\JsonResponse
    {
        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $user_id, 'is_guest' => $user_type, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $user_id, 'is_guest' => $user_type, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method'],
            ]);
            return response()->json(['message' => 'Payment method is updated.'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => 'not found!'],
            ],
        ], 401);
    }
}

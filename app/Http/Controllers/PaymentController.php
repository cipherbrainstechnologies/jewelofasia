<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\CustomerAddress;
use App\User;
use Illuminate\Http\Request;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Traits\Payment;
use Mpdf\Tag\Em;
use App\CentralLogics\PaypalHelper;
use App\Model\Order;
use App\Models\userSubscription;
use App\Model\OrderDetail;
use App\Model\Product;
use Carbon\Carbon;

use function App\CentralLogics\translate;

class PaymentController extends Controller
{
    private $paypalHelper;
    private $front_url = 'https://phpstack-941212-4384366.cloudwaysapps.com/';
    public function __construct(PaypalHelper $paypalHelper){

        if (is_dir('App\Traits') && trait_exists('App\Traits\Payment')) {
            $this->extendWithPaymentGatewayTrait();
        }

        $this->paypalHelper = $paypalHelper;
    }

    private function extendWithPaymentGatewayTrait()
    {
        $extendedControllerClass = $this->generateExtendedControllerClass();
        eval($extendedControllerClass);
    }

    private function generateExtendedControllerClass()
    {
        $baseControllerClass = get_class($this);
        $traitClassName = 'App\Traits\Payment';

        $extendedControllerClass = "
            class ExtendedController extends $baseControllerClass {
                use $traitClassName;
            }
        ";

        return $extendedControllerClass;
    }

    public function payment(Request $request)
    {
        if (session()->has('payment_method') == false) {
            session()->put('payment_method', 'ssl_commerz');
        }

        $params = explode('&&', base64_decode($request['token']));

        foreach ($params as $param) {
            $data = explode('=', $param);
            if ($data[0] == 'customer_id') {
                session()->put('customer_id', $data[1]);
            } elseif ($data[0] == 'callback') {
                session()->put('callback', $data[1]);
            } elseif ($data[0] == 'order_amount') {
                session()->put('order_amount', $data[1]);
            } elseif ($data[0] == 'product_ids') {
                session()->put('product_ids', $data[1]);
            }elseif ($data[0] == 'is_guest') {
                session()->put('is_guest', $data[1]);
            }
        }

        $order_amount = session('order_amount');
        $customer_id = session('customer_id');
        $is_guest = session('is_guest') == 1 ? 1 : 0;

        if (!isset($order_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if ($order_amount < 0) {
            return response()->json(['errors' => ['message' => 'Amount is less than 0']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        //partial payment validation
        if ($request['is_partial'] == 1){
            if ($is_guest == 1){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('partial order does not applicable for guest user')]]], 403);
            }

            $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);

            if (Helpers::get_business_settings('wallet_status') != 1){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('customer_wallet_status_is_disable')]]], 403);
            }
            if (isset($customer) && $customer->wallet_balance < 1){
                return response()->json(['errors' => [['code' => 'payment_method', 'message' => translate('since your wallet balance is less than 1, you can not place partial order')]]], 403);
            }
        }

        $additional_data = [
            'business_name' => Helpers::get_business_settings('restaurant_name') ?? '',
            'business_logo' => asset('storage/app/public/restaurant/' . Helpers::get_business_settings('logo'))
        ];
        //add fund to wallet
        $is_add_fund = $request['is_add_fund'];
        if ($is_add_fund == 1) {
            $add_fund_to_wallet = Helpers::get_business_settings('add_fund_to_wallet');
            if ($add_fund_to_wallet == 0){
                return response()->json(['errors' => ['message' => 'Add fund to wallet is not active']], 403);
            }

            $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);
            if (!isset($customer)) {
                return response()->json(['errors' => ['message' => 'Customer not found']], 403);
            }

            $payer = new Payer($customer['f_name'].' '.$customer['l_name'], $customer['email'], $customer['phone'], '');

            $payment_info = new PaymentInfo(
                success_hook: 'add_fund_success',
                failure_hook: 'add_fund_fail',
                currency_code: Helpers::currency_code(),
                payment_method: $request->payment_method,
                payment_platform: $request->payment_platform,
                payer_id: $customer->id,
                receiver_id: null,
                additional_data: $additional_data,
                payment_amount: $order_amount,
                external_redirect_link: session('callback'),
                attribute: 'add-fund',
                attribute_id: '10001'
            );

            $receiver_info = new Receiver('receiver_name','example.png');
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
            return redirect($redirect_link);
        }


        //order place
        if ($is_guest == 1) {//guest order
            $address = CustomerAddress::where(['user_id' => $customer_id, 'is_guest' => 1])->first();
            if ($address){
                $customer = collect([
                    'f_name' => $address['contact_person_name'] ?? '',
                    'l_name' => '',
                    'phone' => $address['contact_person_number'] ?? '',
                    'email' => '',
                ]);
            }else{
                $customer = collect([
                    'f_name' => 'example',
                    'l_name' => 'customer',
                    'phone' => '+88011223344',
                    'email' => 'example@customer.com',
                ]);
            }
        } else { //normal order
            $customer = User::firstWhere(['id' => $customer_id, 'is_block' => 0]);
            if (!isset($customer)) {
                return response()->json(['errors' => ['message' => 'Customer not found']], 403);
            }
            $customer = collect([
                'f_name' => $customer['f_name'],
                'l_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ]);
        }

        $payer = new Payer($customer['f_name'] . ' ' . $customer['l_name'] , $customer['email'], $customer['phone'], '');

        $payment_info = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_cancel',
            currency_code: Helpers::currency_code(),
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: session('customer_id'),
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $order_amount,
            external_redirect_link: session('callback'),
            attribute: 'order',
            attribute_id: '10001'
        );

        $receiver_info = new Receiver('receiver_name','example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
        return redirect($redirect_link);
    }

    public function subscription_payment(Request $request)
    {
        if (session()->has('payment_method') == false) {
            session()->put('payment_method', 'ssl_commerz');
        }

        $params = explode('&&', base64_decode($request['token']));
        // dd($params);
        $param_array = [];

        foreach ($params as $param) {
            $data = explode('=', $param);
            $param_array[$data[0]] = $data[1];
        }
        $customer_id = !empty($param_array['customer_id']) ? $param_array['customer_id'] : '';
        $plan_id = !empty($param_array['plan_id']) ? $param_array['plan_id'] : '';
        $paypal_product_id = !empty($param_array['paypal_product_id']) ? $param_array['paypal_product_id'] : '';
        $quantity = !empty($param_array['quantity']) ? $param_array['quantity'] : 1;
        $deliveryCharge = !empty($param_array['deliveryCharge']) ? $param_array['deliveryCharge'] : 0;
        $customer = User::find($customer_id);
        $address_id = !empty($param_array['delivery_address_id']) ? $param_array['delivery_address_id'] : 0;
        $address = CustomerAddress::find($address_id);
        $start_date = !empty($param_array['start_date']) ? $param_array['start_date'] : null;
        $order_amount = !empty($param_array['order_amount']) ? $param_array['order_amount'] : 0;
        $payment_info = [
            'quantity' => $quantity,
            'shipping_amount' => $deliveryCharge,
            'given_name' => !empty($customer['f_name']) ? $customer['f_name'] : '',
            'surname' => !empty($customer['l_name']) ? $customer['l_name'] : '',
            'email_address' => !empty($customer['email']) ? $customer['email'] : '',
            'full_name' => (!empty($customer['f_name']) ? $customer['f_name'] : '') . ' ' .  (!empty($customer['l_name']) ? $customer['l_name'] : ''), 
            'postal_code' => !empty($address['zipcode_id']) ? $address['zipcode_id'] : '',
            'address' => !empty($address['address']) ? $address['address'] : '',
            'start_date' => $start_date
        ];
        
        $order = new Order();
        $order_id = 100000 + Order::all()->count() + 1;
        $order->id = $order_id;
        $order->user_id = $customer_id;
        $order->is_guest = 0;
        $order->order_amount = $order_amount;
        $order->order_status = 'paid';
        $order->order_status = 'confirmed';
        $order->payment_method = 'paypal';
        $order->delivery_address_id = !empty($address['id']) ? $address['id'] : null;
        $order->created_at = Carbon::createFromFormat('Y-m-d H:i:s', now());
        $order->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', now());
        $order->checked = 1;
        $order->delivery_charge = !empty($deliveryCharge) ? $deliveryCharge : 0;
        $order->order_type = 'delivery';
        $order->branch_id = 1;
        $order->date = Carbon::parse(now())->format('Y-m-d');
        $order->delivery_date = !empty($start_date) ? Carbon::parse($start_date)->format('Y-m-d') : null;
        $order->delivery_address = json_encode($address);

        $order->save();
        $product = Product::where('paypal_product_id', $paypal_product_id)->first();
        if(!empty($order->id)) {
            $order_detail = new OrderDetail();
            $order_detail->order_id = $order->id;
            $order_detail->product_id = !empty($product->id) ? $product->id : null;
            $order_detail->price = $order_amount;
            $order_detail->product_details = json_encode($product->toArray());
            $order_detail->variation = $product->variations;
            $order_detail->discount_type = 'discount_on_category';
            $order_detail->quantity = $quantity;
            $order_detail->created_at = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $order_detail->updated_at = Carbon::createFromFormat('Y-m-d H:i:s', now());
            $order_detail->unit = $product->unit;
            $order_detail->save();
        }

        $url = $this->front_url .'order-successful/'.$order->id.'/success';
        $payment_info['url'] = $url;

        $res = $this->paypalHelper->add_subscription($plan_id, $payment_info);
        $user_subscription_data = [
            'user_id' => $customer_id,
            'plan_id' => $plan_id,
            'paypal_product_id' => $paypal_product_id,
            'subscription_id' => !empty($res->id) ? $res->id : '',
            'order_id' => $order->id,
            'status' => 1
        ];
        
        userSubscription::create($user_subscription_data);
        $order = Order::find($order->id);
        $order->transaction_reference = !empty($res->id) ? $res->id : '';
        $order->payment_status = 'paid';
        $order->save();
        
        if(!empty($res->links[0]->href)) {
            return redirect($res->links[0]->href);
        } else {
            echo "somethig want to wrong";
        }
    }

    public function success()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
}

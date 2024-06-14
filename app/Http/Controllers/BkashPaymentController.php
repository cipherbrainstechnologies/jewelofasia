<?php

// namespace App\Http\Controllers;

// use App\Models\PaymentRequest;
// use App\Traits\Processor;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
// use app\User;

// class BkashPaymentController extends Controller
// {
//     use Processor;

//     private $config_values;
//     private $base_url;
//     private $app_key;
//     private $app_secret;
//     private $username;
//     private $password;
//     private PaymentRequest $payment;
//     private $user;

//     public function __construct(PaymentRequest $payment, User $user)
//     {
//         $config = $this->payment_config('bkash', 'payment_config');
//         if (!is_null($config) && $config->mode == 'live') {
//             $this->config_values = json_decode($config->live_values);
//         } elseif (!is_null($config) && $config->mode == 'test') {
//             $this->config_values = json_decode($config->test_values);
//         }

//         if ($config) {
//             $this->app_key = $this->config_values->app_key;
//             $this->app_secret = $this->config_values->app_secret;
//             $this->username = $this->config_values->username;
//             $this->password = $this->config_values->password;
//             $this->base_url = ($config->mode == 'live') ? 'https://tokenized.pay.bka.sh/v1.2.0-beta' : 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
//         }

//         $this->payment = $payment;
//         $this->user = $user;
//     }

//     public function getToken()
//     {
//         $post_token = array(
//             'app_key' => $this->app_key,
//             'app_secret' => $this->app_secret
//         );

//         $url = curl_init($this->base_url . '/tokenized/checkout/token/grant');
//         $post_token_json = json_encode($post_token);
//         $header = array(
//             'Content-Type:application/json',
//             'username:' . $this->username,
//             'password:' . $this->password
//         );

//         curl_setopt($url, CURLOPT_HTTPHEADER, $header);
//         curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($url, CURLOPT_POSTFIELDS, $post_token_json);
//         curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

//         $resultdata = curl_exec($url);
//         curl_close($url);

//         $response = json_decode($resultdata, true);

//         if (array_key_exists('msg', $response)) {
//             return $response;
//         }

//         return $response;
//     }

//     public function make_tokenize_payment(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'payment_id' => 'required|uuid'
//         ]);

//         if ($validator->fails()) {
//             return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
//         }

//         $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
//         if (!isset($data)) {
//             return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
//         }
//         $payer = json_decode($data['payer_information']);

//         $response = self::getToken();
//         $auth = $response['id_token'];
//         session()->put('token', $auth);
//         $callbackURL = route('bkash.callback', ['payment_id' => $request['payment_id'], 'token' => $auth]);

//         $requestbody = array(
//             'mode' => '0011',
//             'amount' => round($data->payment_amount, 2),
//             'currency' => 'BDT',
//             'intent' => 'sale',
//             'payerReference' => $payer->phone,
//             'merchantInvoiceNumber' => 'invoice_' . Str::random('15'),
//             'callbackURL' => $callbackURL
//         );

//         $url = curl_init($this->base_url . '/tokenized/checkout/create');
//         $requestbodyJson = json_encode($requestbody);

//         $header = array(
//             'Content-Type:application/json',
//             'Authorization:' . $auth,
//             'X-APP-Key:' . $this->app_key
//         );

//         curl_setopt($url, CURLOPT_HTTPHEADER, $header);
//         curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
//         curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//         $resultdata = curl_exec($url);
//         curl_close($url);

//         $obj = json_decode($resultdata);
//         return redirect()->away($obj->{'bkashURL'});
//     }

//     public function callback(Request $request)
//     {
//         $paymentID = $_GET['paymentID'];
//         $auth = $_GET['token'];
//         $request_body = array(
//             'paymentID' => $paymentID
//         );
//         $url = curl_init($this->base_url . '/tokenized/checkout/execute');

//         $request_body_json = json_encode($request_body);

//         $header = array(
//             'Content-Type:application/json',
//             'Authorization:' . $auth,
//             'X-APP-Key:' . $this->app_key
//         );

//         curl_setopt($url, CURLOPT_HTTPHEADER, $header);
//         curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
//         curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($url, CURLOPT_POSTFIELDS, $request_body_json);
//         curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
//         $resultdata = curl_exec($url);
//         curl_close($url);
//         $obj = json_decode($resultdata);

//         if ($obj->statusCode == '0000') {

//             $this->payment::where(['id' => $request['payment_id']])->update([
//                 'payment_method' => 'bkash',
//                 'is_paid' => 1,
//                 'transaction_id' => $obj->trxID ?? null,
//             ]);

//             $data = $this->payment::where(['id' => $request['payment_id']])->first();

//             if (isset($data) && function_exists($data->success_hook)) {
//                 call_user_func($data->success_hook, $data);
//             }

//             return $this->payment_response($data,'success');
//         } else {
//             $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
//             if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
//                 call_user_func($payment_data->failure_hook, $payment_data);
//             }
//             return $this->payment_response($payment_data,'fail');
//         }
//     }


// }


namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
//use app\User;

class BkashPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;
    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment)
    {
        $config = $this->payment_config('afterpay', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        if ($config) {
            //$this->app_key = $this->config_values->app_key;
            $this->app_secret = $this->config_values->app_secret;
            $this->username = $this->config_values->username;
            $this->password = $this->config_values->password;
            $this->base_url = ($config->mode == 'live') ? 'https://global-api.afterpay.com' : 'https://global-api-sandbox.afterpay.com';
        }

        $this->payment = $payment;
       
    }

    public function getToken()
    {
        $url = "https://merchant-auth.afterpay.com/v2/oauth2/token";
        $post_token = array(
           'grant_type' => 'client_credentials',
            'client_id' => $this->password,
            'client_secret' => $this->$this->app_secret,
            "scop"=> 'merchant_api_v2'
        );
       
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
        
    }

    public function make_tokenize_payment(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id'], 'is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
       
        $nameParts = explode(' ',json_decode($data->payer_information)->name);
        // $response = $this->getToken();
        $auth =  base64_encode($this->password . ':' . $this->app_secret);
        // session()->put('token', $auth);

        $requestbody = [
            'amount' => [
                'amount' => round($data->payment_amount, 2),
                'currency' => 'AUD' 
            ],
            'consumer' => [
                'phoneNumber' => json_decode($data->payer_information)->phone,
                'givenNames' => $nameParts[0],
                'surname' => $nameParts[1] ,
            ],
            'merchantReference' => $data->id,
            'merchant' => [
                'redirectConfirmUrl' => route('bkash.callback', ['payment_id' => $data->id]),
                'redirectCancelUrl' => route('bkash.callback', ['payment_id' => $data->id])
            ]
        ];

        $curl = curl_init($this->base_url . '/v2/checkouts');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestbody));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . $auth,
        ]);

        $resultdata = curl_exec($curl);
        // curl_close($curl);
        // dd($resultdata);

        $obj = json_decode($resultdata);

        if (isset($obj->token)) {
            session()->put('orderToken', $obj->token);
            return redirect()->away($obj->redirectCheckoutUrl);
        } else {
            return response()->json(['error' => 'Failed to initiate payment'], 500);
        }
       
    }

    public function callback(Request $request)
    {
        $orderToken = session()->get('orderToken');
        $auth = base64_encode($this->password . ':' . $this->app_secret);

        $url = $this->base_url . '/v2/payments/capture';
        $request_body = [
            'token' => $orderToken,
            'merchantReference' => $request->input('payment_id')
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request_body));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . $auth,
        ]);

        $resultdata = curl_exec($curl);
        curl_close($curl);

        $obj = json_decode($resultdata);

    // Log the entire response for debugging
        \Log::info('Afterpay API Response: ', (array)$obj);

    // Check if the response is valid and contains the expected properties
        if (is_object($obj) && isset($obj->status)) {
            if ($obj->status == 'APPROVED') {
                $this->payment::where(['id' => $request->input('payment_id')])->update([
                    'payment_method' => 'afterpay',
                    'is_paid' => 1,
                    'transaction_id' => $obj->id ?? null,
                ]);

                $data = $this->payment::where(['id' => $request->input('payment_id')])->first();

                if (isset($data) && function_exists($data->success_hook)) {
                    call_user_func($data->success_hook, $data);
                }

                return $this->payment_response($data, 'success');
            } 
        }
        else {
           $payment_data = $this->payment::where(['id' => $request->input('payment_id')])->first();
           if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
               call_user_func($payment_data->failure_hook, $payment_data);
           }
           return $this->payment_response($payment_data, 'fail');
        }
    }


}

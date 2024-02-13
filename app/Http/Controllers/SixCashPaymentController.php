<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Brian2694\Toastr\Facades\Toastr;


class SixCashPaymentController extends Controller
{
    private $public_key;
    private $secret_key;
    private $merchant_number;
    private $base_url;
    private $payment_verification_url;

    public function __construct()
    {
        $config = Helpers::get_business_settings('6cash');

        $six_public_key = $config['public_key'];
        $six_secret_key = $config['secret_key'];
        $six_merchant_number = $config['merchant_number'];
        $six_base_url = 'https://6cash-admin.6am.one/api/v1/create-payment-order';
        $payment_verification_url = 'https://6cash-admin.6am.one/api/v1/payment-verification';

        $this->public_key = $six_public_key;
        $this->secret_key = $six_secret_key;
        $this->merchant_number = $six_merchant_number;
        $this->base_url = $six_base_url;
        $this->payment_verification_url = $payment_verification_url;
    }

    public function make_payment(Request $request)
    {
        $url = $request['callback'];

        session()->put('callback_from_web',$url);

        $response = Http::post($this->base_url, [
            'public_key'=> $this->public_key,
            'secret_key'=> $this->secret_key,
            'merchant_number'=> $this->merchant_number,
            'amount'=> $request->order_amount,
        ])->json();

        if($response['status'] == 'merchant_not_found'){
            Toastr::success(translate('Merchant not found!'));
            return back();
        }

        if($response['status'] == 'payment_created'){
            return redirect()->away($response['redirect_url']. '&callback=' . $url);
        }
    }

    public function callback(Request $request)
    {
        $callback= session('callback_from_web');
        $transaction_id= $request->transaction_id;

        $token_string = 'payment_method=6cash&&transaction_reference=' . $transaction_id;

        $response = Http::post($this->payment_verification_url, [
            'public_key'=> $this->public_key,
            'secret_key'=> $this->secret_key,
            'merchant_number'=> $this->merchant_number,
            'transaction_id' => $transaction_id,
        ])->json();

        if(isset($response['errors'])){
            if ($callback != null) {
                return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
            }
        }

        $payment_record = $response['payment_record'] ?? null;

        //if success
        if(isset($payment_record) && $payment_record['is_paid'] == 1) {
            if ($callback != null) {
                return redirect($callback . '/success' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-success', ['token' => base64_encode($token_string)]);
            }
            //if failed
        } else {
            if ($callback != null) {
                return redirect($callback . '/fail' . '?token=' . base64_encode($token_string));
            } else {
                return \redirect()->route('payment-fail', ['token' => base64_encode($token_string)]);
            }
        }

    }

}

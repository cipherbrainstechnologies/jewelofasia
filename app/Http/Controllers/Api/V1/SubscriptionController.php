<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Plan;
use PayPal\Api\Agreement;
use PayPal\Api\Item;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use Illuminate\Support\Facades\DB;
use PayPalCheckoutSdk\Products\ProductsCreateRequest;

class SubscriptionController extends Controller
{
    private $apiContext;

    public function __construct()
    {
        $paypal = DB::table('addon_settings')->where('key_name', 'paypal')->first();
        $live_cred = json_decode($paypal->live_values);
        $test_cred = json_decode($paypal->test_values);
        $client_id = ($paypal->mode === 'live') ?  $live_cred->client_id: $test_cred->client_id;
        $secret_id = ($paypal->mode === 'live') ?  $live_cred->client_secret: $test_cred->client_secret;
        
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $client_id,
                $secret_id
            )
        );
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    public function get_products() 
    {
        dd($this->apiContext);
    }
}

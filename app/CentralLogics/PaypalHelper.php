<?php

namespace App\CentralLogics;

use App\Model\BusinessSetting;
use App\Model\CategoryDiscount;
use App\Model\Currency;
use App\Model\DMReview;
use App\Model\Order;
use App\Model\Review;
use App\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class PaypalHelper
{
    protected $client;
    protected $client_id;
    protected $secret_id;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api-m.sandbox.paypal.com/v1/oauth2/',
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
        // dd($this->client);
        $paypal = DB::table('addon_settings')->where('key_name', 'paypal')->first();
        $live_cred = json_decode($paypal->live_values);
        $test_cred = json_decode($paypal->test_values);
        $this->client_id = ($paypal->mode === 'live') ?  $live_cred->client_id: $test_cred->client_id;
        $this->secret_id = ($paypal->mode === 'live') ?  $live_cred->client_secret: $test_cred->client_secret;
    }

    public function getToken()
    {
        $response = $this->client->post('token', [
            'auth' => [$this->client_id, $this->secret_id],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);
        // dd(json_decode($response->getBody()->getContents(), true)['access_token']); 
        return json_decode($response->getBody()->getContents(), true)['access_token'];
    }

    public function createProduct($data)
    {
        $create_product = new Client();
        $response =  $create_product->post('https://api-m.sandbox.paypal.com/v1/catalogs/products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                "name" => $data['name'],
                "description" => $data['description'],
                "type" => "PHYSICAL",
                "category" => "FOOD_PRODUCTS",
                "image_url" => "https://img.freepik.com/free-photo/tasty-burger-isolated-white-background-fresh-hamburger-fastfood-with-beef-cheese_90220-1063.jpg?size=338&ext=jpg&ga=GA1.1.87170709.1707782400&semt=sph",
                "home_url" => "https://www.jewelofasia.com.au/"
            ]
        ]);
        $paypal_product = json_decode($response->getBody()->getContents());
        return $paypal_product->id;
    }

    public function createPlan($paypal_product_id ,$plan, $interval_count)
    {
        $create_product = new Client();
        $response =  $create_product->post('https://api-m.sandbox.paypal.com/v1/billing/plans', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                "product_id" => $paypal_product_id,
                "name" => $plan,
                "status"=> "ACTIVE",
                "billing_cycles" => [
                    [
                        "frequency" => [
                            "interval_unit" => $plan,
                            "interval_count" => $interval_count,
                        ],
                        "tenure_type" => "TRIAL",
                        "sequence" => 1,
                        "total_cycles"=> 1,
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => "0",
                                "currency_code" => "AUD"
                            ],
                            "version" => 1,
                        ],
                    ],
                    "payment_preferences" => [
                        "auto_bill_outstanding" => true,
                        "setup_fee" => [
                            "value" => "0",
                            "currency_code" => "AUD"
                        ],
                        "setup_fee_failure_action" => "CONTINUE",
                        "payment_failure_threshold"=> 1
                    ]
                ],
            ]
        ]);
        $paypal_plan = json_decode($response->getBody()->getContents());
        return $paypal_plan->id;
    }
}
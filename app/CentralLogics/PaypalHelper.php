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
use Illuminate\Support\Facades\Http;

class PaypalHelper
{
    protected $client;
    protected $client_id;
    protected $secret_id;
    private $front_url = 'https://phpstack-941212-4384366.cloudwaysapps.com/';

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

// create product
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
                "image_url" => "https://media.istockphoto.com/id/1396814518/vector/image-coming-soon-no-photo-no-thumbnail-image-available-vector-illustration.jpg?s=612x612&w=0&k=20&c=hnh2OZgQGhf0b46-J2z7aHbIWwq8HNlSDaNp2wn_iko=",
                "home_url" => "https://www.jewelofasia.com.au/"
            ]
        ]);
        $paypal_product = json_decode($response->getBody()->getContents());
        $data = [
            'paypal_product_id' => $paypal_product->id,
            'paypal_access_token' => $this->getToken(),
        ];
        return $data;
    }

    // list all paypal product list
    public function listProducts($page_size = 10, $page = 1, $total_required = "false")
    {
        $client = new Client();
        $response = $client->get('https://api-m.sandbox.paypal.com/v1/catalogs/products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => [
                'page_size' => $page_size,
                'page' => $page,
                'total_required' => $total_required,
            ],
        ]);

        $paypalProducts = json_decode($response->getBody()->getContents(), true);
        return $paypalProducts;
    }


    // Show particular paypal product detail
    public function showProductDetail($productId)
    {        
        $client = new Client();
        $response = $client->get('https://api-m.sandbox.paypal.com/v1/catalogs/products/' . $productId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $paypalProduct = json_decode($response->getBody()->getContents(), true);
        // $paypalProduct = json_encode($response->getBody()->getContents(), true);
        return $paypalProduct;
    }

    // Subscription 

    // Create product plan
    public function createPlan($paypal_product_id ,$plan, $frequency, $interval_count, $price, $token)
    {
        try {
            $create_plan = new Client();
            $response =  $create_plan->post('https://api-m.sandbox.paypal.com/v1/billing/plans', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                "product_id" => $paypal_product_id,
                "name" => $plan,
                "status" => "ACTIVE",
                "billing_cycles" => [
                    [
                        "frequency" => [
                            "interval_unit" => $frequency,
                            "interval_count" => $interval_count
                        ],
                        "tenure_type" => "REGULAR",
                        "sequence" => 1,
                        "total_cycles" => 1,
                        "pricing_scheme" => [
                            "fixed_price" => [
                                "value" => !empty($price) ? $price : "0",
                                "currency_code" => "AUD"
                            ]
                        ]
                    ]
                ],
                "payment_preferences" => [
                    "auto_bill_outstanding" => true,
                    "setup_fee" => [
                        "value" => "0",
                        "currency_code" => "AUD"
                    ],
                    "setup_fee_failure_action" => "CONTINUE",
                    "payment_failure_threshold" => 3
                ],
                "taxes" => [
                    "percentage" => "0",
                    "inclusive" => true
                ]
            ]
        ]);
        $paypal_plan = json_decode($response->getBody()->getContents());
        return $paypal_plan->id;
        } catch(Exception $e) {
            dd($e);
        }
    }
    

    //  List out Subscription plans
    public function listSubscriptionPlans($productId=Null,$planIds = Null,$page_size = 10, $page = 1, $total_required = "false")
    {

        $client = new Client();
        $response = $client->get('https://api-m.sandbox.paypal.com/v1/billing/plans', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => [
                'product_id'=> $productId,
                'plan_ids' => $planIds,
                'page_size' => $page_size,
                'page' => $page,
                'total_required' => $total_required,
            ],
        ]);

        $paypalSubscriptionPlans = json_decode($response->getBody()->getContents(), true);
        
        return $paypalSubscriptionPlans;
    }

    public function update_plan_price($id, $price, $token) 
    {
        dd($id, $token);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api-m.sandbox.paypal.com/v1/billing/plans/'.$id.'/update-pricing-schemes', [
            "pricing_schemes" => [
                [
                    "billing_cycle_sequence" => 1,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => !empty($price) ? $price : "0",
                            "currency_code" => "AUD"
                        ]
                    ]
                ],
            ]
        ]);
        
        // Handle response
        if ($response->successful()) {
            // Request was successful
            $responseData = $response->json();
           dd($responseData);
        } else {
            // Request failed
            $errorData = $response->json();
            dd($errorData);
            // Handle error
        }
    }

    public function add_subscription($plan_id, $data)
    {
        // dd($data);
        try {
            $create_plan = new Client();
            $response =  $create_plan->post('https://api-m.sandbox.paypal.com/v1/billing/subscriptions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                "plan_id" => $plan_id,
                "quantity" => $data['quantity'],
                "shipping_amount" => [
                    "currency_code" => "AUD",
                    "value" => $data['shipping_amount']
                ],
                "subscriber" => [
                    "name" => [
                        "given_name" => $data['given_name'],
                        "surname" => $data['surname']
                    ],
                    "email_address" => $data['email_address'],
                    "shipping_address" => [
                        "name" => [
                            "full_name" => $data['full_name']
                        ],
                        "address" => [
                            "address_line_1" => $data['address'],
                            "address_line_2" => "",
                            "admin_area_2" => "",
                            "admin_area_1" => "",
                            "postal_code" =>  $data['postal_code'],
                            "country_code" => "AU"
                        ]
                    ]
                ],
                "application_context" => [
                    "brand_name" => "walmart",
                    "locale" => "en-US",
                    "shipping_preference" => "SET_PROVIDED_ADDRESS",
                    "user_action" => "SUBSCRIBE_NOW",
                    "payment_method" => [
                        "payer_selected" => "PAYPAL",
                        "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED"
                    ],
                    "return_url" => $this->front_url,
                    "cancel_url" => "https://example.com/cancelUrl"
                ]
            ]
        ]);
        $paypal_response = json_decode($response->getBody()->getContents());
        return $paypal_response;
        
        } catch(Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function update_product($id, $data, $token)
    {
        // dd($id, $data, $token);
        $create_product = new Client();
        $response =  $create_product->patch('https://api-m.sandbox.paypal.com/v1/catalogs/products/'. $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getToken(),
                'Prefer' => 'return=representation',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                [
                    "op" => "replace",
                    "path" => "/name",
                    "value" => $data['name']
                ],
                [
                    "op" => "replace",
                    "path" => "/description",
                    "value" => $data['description']
                ],
            ]
        ]);
    }

}
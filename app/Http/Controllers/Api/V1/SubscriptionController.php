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
use App\CentralLogics\PaypalHelper;

class SubscriptionController extends Controller
{
   private $paypalHelper;
   public function __construct(PaypalHelper $paypalHelper){
        $this->paypalHelper = $paypalHelper;
   }

   public function getSubscriptionProductList(Request $request){
        return $this->paypalHelper->listProducts();
   }

    public function showProductDetail($productId){
        return $this->paypalHelper->showProductDetail($productId);
    }

    public function listSubscriptionPlans(){
        return $this->paypalHelper->listSubscriptionPlans();
    }

    public function add_subscription(Request $request)
    {
        $this->paypalHelper->add_subscription($request->plan_id);
    }

}

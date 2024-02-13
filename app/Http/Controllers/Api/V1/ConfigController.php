<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\FlashDeal;
use App\Model\SocialMedia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class ConfigController extends Controller
{
    public function configuration(): \Illuminate\Http\JsonResponse
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = json_decode(BusinessSetting::where(['key' => 'cash_on_delivery'])->first()->value, true);
        $dp = json_decode(BusinessSetting::where(['key' => 'digital_payment'])->first()->value, true);

        //addon settings publish status
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $active_addon_payment_lists = $published_status == 1 ? $this->getPaymentMethods() : $this->getDefaultPaymentMethods();

        $digital_payment_status = BusinessSetting::where(['key' => 'digital_payment'])->first()->value;
        $digital_payment_status_value = json_decode($digital_payment_status, true);

        $digital_payment_infos = array(
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'plugin_payment_gateways' =>  $published_status ? "true" : "false",
            'default_payment_gateways' =>  $published_status ? "false" : "true"
        );

        $dm_config = Helpers::get_business_settings('delivery_management');
        $delivery_management = array(
            "status" => (int) $dm_config['status'],
            "min_shipping_charge" => (float) $dm_config['min_shipping_charge'],
            "shipping_per_km" => (float) $dm_config['shipping_per_km'],
        );
        $play_store_config = Helpers::get_business_settings('play_store_config');
        $app_store_config = Helpers::get_business_settings('app_store_config');

        $cookies_config = Helpers::get_business_settings('cookies');
        $cookies_management = array(
            "status" => (int) $cookies_config['status'],
            "text" => $cookies_config['text'],
        );

        $offline_payment = json_decode(BusinessSetting::where(['key' => 'offline_payment'])->first()->value, true);

        $flash_deals = FlashDeal::active()->where('deal_type','flash_deal')->first();
        if ($flash_deals){
            $flash_deal_product_status = 1;
        }else{
            $flash_deal_product_status = 0;
        }

        $apple = Helpers::get_business_settings('apple_login');
        $apple_login = array(
            'login_medium' => $apple['login_medium'],
            'status' => (integer)$apple['status'],
            'client_id' => $apple['client_id']
        );

        $firebase_otp_verification = Helpers::get_business_settings('firebase_otp_verification');

        $email_verification = (integer)Helpers::get_business_settings('email_verification') ?? 0;
        $phone_verification = (integer)Helpers::get_business_settings('phone_verification') ?? 0;

        $status = 0;
        $type = '';
        if ($email_verification == 1) {
            $status = 1;
            $type = 'email';
        } elseif ($phone_verification == 1) {
            $type = ($firebase_otp_verification && $firebase_otp_verification['status'] == 1) ? 'firebase' : 'phone';
            $status = 1;
        }

        $customer_verification = [
            'status' => $status,
            'type' => $type,
        ];

        return response()->json([
            'ecommerce_name'              => BusinessSetting::where(['key' => 'restaurant_name'])->first()->value,
            'ecommerce_logo'              => BusinessSetting::where(['key' => 'logo'])->first()->value,
            'ecommerce_address'           => BusinessSetting::where(['key' => 'address'])->first()->value,
            'ecommerce_phone'             => BusinessSetting::where(['key' => 'phone'])->first()->value,
            'ecommerce_email'             => BusinessSetting::where(['key' => 'email_address'])->first()->value,
            'ecommerce_location_coverage' => Branch::where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value'         => (float) BusinessSetting::where(['key' => 'minimum_order_value'])->first()->value,
            'self_pickup'                 => (int) BusinessSetting::where(['key' => 'self_pickup'])->first()->value,
            'base_urls'                   => [
                'product_image_url'      => asset('storage/app/public/product'),
                'customer_image_url'     => asset('storage/app/public/profile'),
                'banner_image_url'       => asset('storage/app/public/banner'),
                'category_image_url'     => asset('storage/app/public/category'),
                'review_image_url'       => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'ecommerce_image_url'    => asset('storage/app/public/restaurant'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url'         => asset('storage/app/public/conversation'),
                'flash_sale_image_url'   => asset('storage/app/public/offer'),
                'gateway_image_url'      => asset('storage/app/public/payment_modules/gateway_image'),
                'payment_image_url'      => asset('public/assets/admin/img/payment'),
            ],
            'currency_symbol'             => $currency_symbol,
            'delivery_charge'             => (float) BusinessSetting::where(['key' => 'delivery_charge'])->first()->value,
            'delivery_management'         => $delivery_management,
            'branches'                    => Branch::active()->get(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status']),
            'terms_and_conditions' => BusinessSetting::where(['key' => 'terms_and_conditions'])->first()->value,
            'privacy_policy' => BusinessSetting::where(['key' => 'privacy_policy'])->first()->value,
            'about_us' => BusinessSetting::where(['key' => 'about_us'])->first()->value,
            'faq' => BusinessSetting::where(['key' => 'faq'])->first()->value,
            'email_verification' => (boolean)Helpers::get_business_settings('email_verification') ?? 0,
            'phone_verification' => (boolean)Helpers::get_business_settings('phone_verification') ?? 0,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'country' => Helpers::get_business_settings('country') ?? 'BD',
            'play_store_config' => [
                "status"=> isset($play_store_config) ? (boolean) $play_store_config['status'] : false,
                "link"=> isset($play_store_config) ? $play_store_config['link'] : null,
                "min_version"=> isset($play_store_config) && array_key_exists('min_version', $play_store_config) ? $play_store_config['min_version'] : null
            ],
            'app_store_config' => [
                "status"=> isset($app_store_config) ? (boolean) $app_store_config['status'] : false,
                "link"=> isset($app_store_config) ? $app_store_config['link'] : null,
                "min_version"=> isset($app_store_config) && array_key_exists('min_version', $play_store_config) ? $app_store_config['min_version'] : null
            ],
            'social_media_link' => SocialMedia::orderBy('id', 'desc')->active()->get(),
            'software_version' => (string)env('SOFTWARE_VERSION')??null,
            'footer_text' => Helpers::get_business_settings('footer_text'),
            'decimal_point_settings' => (string)Helpers::get_business_settings('decimal_point_settings')??'0',
            'time_format' => (string)Helpers::get_business_settings('time_format')??'24',
            'social_login' => [
                'google' => (integer)BusinessSetting::where(['key' => 'google_social_login'])->first()->value,
                'facebook' => (integer)BusinessSetting::where(['key' => 'facebook_social_login'])->first()->value,
            ],
            'wallet_status' => (integer)BusinessSetting::where(['key' => 'wallet_status'])->first()->value,
            'loyalty_point_status' => (integer)BusinessSetting::where(['key' => 'loyalty_point_status'])->first()->value,
            'ref_earning_status' => (integer)BusinessSetting::where(['key' => 'ref_earning_status'])->first()->value,
            'loyalty_point_exchange_rate' => (float)(BusinessSetting::where(['key' => 'loyalty_point_exchange_rate'])->first()->value ?? 0),
            'ref_earning_exchange_rate' => (float)(BusinessSetting::where(['key' => 'ref_earning_exchange_rate'])->first()->value ?? 0),
            'loyalty_point_item_purchase_point' => (float)BusinessSetting::where(['key' => 'loyalty_point_percent_on_item_purchase'])->first()->value,
            'loyalty_point_minimum_point' => (float)(BusinessSetting::where(['key' => 'loyalty_point_minimum_point'])->first()->value ?? 0),
            'free_delivery_over_amount' => (float)Helpers::get_business_settings('free_delivery_over_amount') ?? 0,
            'maximum_amount_for_cod_order' => (float)Helpers::get_business_settings('maximum_amount_for_cod_order') ?? 0,
            'cookies_management' => $cookies_management,
            'product_vat_tax_status' => (string)Helpers::get_business_settings('product_vat_tax_status'),
            'maximum_amount_for_cod_order_status' => (integer)(Helpers::get_business_settings('maximum_amount_for_cod_order_status')?? 0),
            'free_delivery_over_amount_status' => (integer)(Helpers::get_business_settings('free_delivery_over_amount_status') ?? 0),
            'cancellation_policy' => BusinessSetting::where(['key' => 'cancellation_policy'])->first()->value ?? '',
            'refund_policy' => BusinessSetting::where(['key' => 'refund_policy'])->first()->value ?? '',
            'return_policy' => BusinessSetting::where(['key' => 'return_policy'])->first()->value ?? '',
            'cancellation_policy_status' => (integer)(Helpers::get_business_settings('cancellation_policy_status') ?? 0),
            'refund_policy_status' => (integer)(Helpers::get_business_settings('refund_policy_status') ?? 0),
            'return_policy_status' => (integer)(Helpers::get_business_settings('return_policy_status') ?? 0),
            'whatsapp' => json_decode(BusinessSetting::where(['key' => 'whatsapp'])->first()->value, true),
            'telegram' => json_decode(BusinessSetting::where(['key' => 'telegram'])->first()->value, true),
            'messenger' => json_decode(BusinessSetting::where(['key' => 'messenger'])->first()->value, true),
            'featured_product_status' => (integer)(Helpers::get_business_settings('featured_product_status') ?? 0),
            'trending_product_status' => (integer)(Helpers::get_business_settings('trending_product_status') ?? 0),
            'most_reviewed_product_status' => (integer)(Helpers::get_business_settings('most_reviewed_product_status') ?? 0),
            'recommended_product_status' => (integer)(Helpers::get_business_settings('recommended_product_status') ?? 0),
            'flash_deal_product_status' => $flash_deal_product_status,
            'toggle_dm_registration' => (integer)(Helpers::get_business_settings('dm_self_registration') ?? 0),
            'otp_resend_time' => Helpers::get_business_settings('otp_resend_time') ?? 60,
            'digital_payment_info' => $digital_payment_infos,
            'digital_payment_status' => (integer)$digital_payment_status_value['status'],
            'active_payment_method_list' => (integer)$digital_payment_status_value['status'] == 1 ? $active_addon_payment_lists : [],
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'offline_payment' => $offline_payment['status'] == 1 ? 'true' : 'false',
            'guest_checkout' => (integer)(Helpers::get_business_settings('guest_checkout') ?? 0),
            'partial_payment' => (integer)(Helpers::get_business_settings('partial_payment') ?? 0),
            'partial_payment_combine_with' => (string)Helpers::get_business_settings('partial_payment_combine_with'),
            'add_fund_to_wallet' => (integer)(Helpers::get_business_settings('add_fund_to_wallet') ?? 0),
            'apple_login' => $apple_login,
            'firebase_otp_verification_status' => (integer)($firebase_otp_verification ? $firebase_otp_verification['status'] : 0),
            'customer_verification' => $customer_verification
        ]);
    }

    private function getPaymentMethods()
    {
        // Check if the addon_settings table exists
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')->where('settings_type', 'payment_config')->get();
        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additional_data?->gateway_title,
                    'gateway_image' => $additional_data?->gateway_image
                ];
            }
        }
        return $data;
    }

    private function getDefaultPaymentMethods()
    {
        // Check if the addon_settings table exists
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')
            ->whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paystack','paymob_accept','flutterwave','bkash','mercadopago'])
            ->get();

        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additional_data?->gateway_title,
                    'gateway_image' => $additional_data?->gateway_image
                ];
            }
        }
        return $data;
    }
}

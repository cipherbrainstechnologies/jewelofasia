<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Model\Admin;
use App\Model\AdminRole;
use App\Model\BusinessSetting;
use App\Traits\ActivationClass;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;

class UpdateController extends Controller
{
    use ActivationClass;

    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        Helpers::setEnvironmentValue('SOFTWARE_ID', 'MzI3OTE2MzE=');
        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '7.2');
        Helpers::setEnvironmentValue('APP_NAME', 'grofresh');
        Helpers::setEnvironmentValue('APP_MODE', 'live');

        if ($this->actch()) {
            return redirect(base64_decode('aHR0cHM6Ly82YW10ZWNoLmNvbS9zb2Z0d2FyZS1hY3RpdmF0aW9u'));
        }

        Artisan::call('migrate', ['--force' => true]);
        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        Artisan::call('optimize:clear');

        if (BusinessSetting::where(['key' => 'terms_and_conditions'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'terms_and_conditions',
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'razor_pay'])->first() == false) {
            BusinessSetting::insert([
                'key' => 'razor_pay',
                'value' => '{"status":"1","razor_key":"","razor_secret":""}'
            ]);
        }
        if (BusinessSetting::where(['key' => 'minimum_order_value'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1
            ]);
        }

        //for modified language [new multi lang in admin]
        $languages = Helpers::get_business_settings('language');
        $lang_array = [];
        $lang_flag = false;

        foreach ($languages as $key => $language) {
            if (gettype($language) != 'array') {
                $lang = [
                    'id' => $key + 1,
                    'name' => $language,
                    'direction' => 'ltr',
                    'code' => $language,
                    'status' => 1,
                    'default' => $language == 'en' ? true : false,
                ];

                array_push($lang_array, $lang);
                $lang_flag = true;
            }
        }
        if ($lang_flag == true) {
            BusinessSetting::where('key', 'language')->update([
                'value' => $lang_array
            ]);
        }
        //lang end

        if (BusinessSetting::where(['key' => 'time_zone'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
                'value' => 'Pacific/Midway'
            ]);
        }
        if (BusinessSetting::where(['key' => 'decimal_point_settings'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'decimal_point_settings'], [
                'value' => 2
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'phone_verification'], [
            'value' => 0
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'msg91_sms'], [
            'key' => 'msg91_sms',
            'value' => '{"status":0,"template_id":null,"authkey":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => '2factor_sms'], [
            'key' => '2factor_sms',
            'value' => '{"status":"0","api_key":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'nexmo_sms'], [
            'key' => 'nexmo_sms',
            'value' => '{"status":0,"api_key":null,"api_secret":null,"signature_secret":"","private_key":"","application_id":"","from":null,"otp_template":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'twilio_sms'], [
            'key' => 'twilio_sms',
            'value' => '{"status":0,"sid":null,"token":null,"from":null,"otp_template":null}'
        ]);
        if (BusinessSetting::where(['key' => 'pagination_limit'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
                'value' => 10
            ]);
        }
        if (BusinessSetting::where(['key' => 'map_api_key'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
                'value' => ''
            ]);
        }
        if (BusinessSetting::where(['key' => 'delivery_management'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode([
                    'status' => 0,
                    'min_shipping_charge' => 0,
                    'shipping_per_km' => 0,
                ]),
            ]);
        }
        if (BusinessSetting::where(['key' => 'recaptcha'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
                'value' => '{"status":"0","site_key":"","secret_key":""}'
            ]);
        }

        if (BusinessSetting::where(['key' => 'time_format'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_format'], [
                'value' => '24'
            ]);
        }

        if (BusinessSetting::where(['key' => 'play_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $play_store_config = Helpers::get_business_settings('play_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $play_store_config['status'],
                    'link' => $play_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        if (BusinessSetting::where(['key' => 'app_store_config'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        } else {
            $app_store_config = Helpers::get_business_settings('app_store_config');
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $app_store_config['status'],
                    'link' => $app_store_config['link'],
                    'min_version' => "1",
                ])
            ]);
        }

        if (BusinessSetting::where(['key' => 'minimum_stock_limit'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_stock_limit'], [
                'value' => 1
            ]);
        }

        if (BusinessSetting::where(['key' => 'faq'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'faq'], [
                'value' => null
            ]);
        }

        if (BusinessSetting::where(['key' => 'google_social_login'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'google_social_login'], [
                'value' => 1
            ]);
        }

        if (BusinessSetting::where(['key' => 'facebook_social_login'])->first() == false) {
            DB::table('business_settings')->updateOrInsert(['key' => 'facebook_social_login'], [
                'value' => 1
            ]);
        }

        //for role management
        $admin_role = AdminRole::get()->first();
        if (!$admin_role) {
            DB::table('admin_roles')->insertOrIgnore([
                'id' => 1,
                'name' => 'Master Admin',
                'module_access' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $admin = Admin::get()->first();
        if ($admin) {
            $admin->admin_role_id = 1;
            $admin->save();
        }


        $mail_config = \App\CentralLogics\Helpers::get_business_settings('mail_config');
        BusinessSetting::where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => 0,
                "name" => $mail_config['name'],
                "host" => $mail_config['host'],
                "driver" => $mail_config['driver'],
                "port" => $mail_config['port'],
                "username" => $mail_config['username'],
                "email_id" => $mail_config['email_id'],
                "encryption" => $mail_config['encryption'],
                "password" => $mail_config['password']
            ]),
        ]);

        DB::table('branches')->insertOrIgnore([
            'id' => 1,
            'name' => 'Main Branch',
            'email' => 'main@gmail.com',
            'password' => '',
            'coverage' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if (!BusinessSetting::where(['key' => 'wallet_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'wallet_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'ref_earning_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'ref_earning_status'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_exchange_rate'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_exchange_rate'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'ref_earning_exchange_rate'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'ref_earning_exchange_rate'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_percent_on_item_purchase'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_percent_on_item_purchase'], [
                'value' => '0'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'loyalty_point_minimum_point'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'loyalty_point_minimum_point'], [
                'value' => '1'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'free_delivery_over_amount'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'free_delivery_over_amount'], [
                'value' => '2000'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_amount_for_cod_order'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_amount_for_cod_order'], [
                'value' => '1000'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'cookies'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'cookies'], [
                'value' => '{"status":"1","text":"Allow Cookies for this site"}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'offline_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'offline_payment'], [
                'value' => '{"status":"1"}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'product_vat_tax_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'product_vat_tax_status'], [
                'value' => 'excluded'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'whatsapp'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'whatsapp'], [
                'value' => '{"status":"0","number":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'telegram'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'telegram'], [
                'value' => '{"status":"0","user_name":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'messenger'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'messenger'], [
                'value' => '{"status":"0","user_name":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'featured_product_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'featured_product_status'], [
                'value' => '1'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'trending_product_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'trending_product_status'], [
                'value' => '1'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'most_reviewed_product_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'most_reviewed_product_status'], [
                'value' => '1'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'recommended_product_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recommended_product_status'], [
                'value' => '1'
            ]);
        }

        //user referral code
        $users = User::whereNull('referral_code')->get();
        foreach ($users as $user) {
            $user->referral_code = Helpers::generate_referer_code();
            $user->save();
        }

        if (!BusinessSetting::where(['key' => 'fav_icon'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        if (!BusinessSetting::where(['key' => 'dm_self_registration'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'dm_self_registration'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_otp_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_otp_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'otp_resend_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'otp_resend_time'], [
                'value' => 60
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_block_time'], [
                'value' => 600
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_login_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_login_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_login_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_login_block_time'], [
                'value' => 600
            ]);
        }

        //version 7.1
        if (!BusinessSetting::where(['key' => 'guest_checkout'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'guest_checkout'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'partial_payment_combine_with'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment_combine_with'], [
                'value' => 'all'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'deliveryman_order_processing_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'deliveryman_order_processing_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_fund_wallet_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_fund_wallet_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'add_fund_wallet_bonus_message'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'add_fund_wallet_bonus_message'], [
                'value' => '{"status":0,"message":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'apple_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"status":0, "login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":""}'
            ]);
        }

        //new database table
        try {
            if (!Schema::hasTable('addon_settings')) {
                $sql = File::get(base_path($request['path'] . 'database/addon/addon_settings.sql'));
                DB::unprepared($sql);
                $this->set_payment_data();
                $this->set_sms_data();
            }
            if (!Schema::hasTable('payment_requests')) {
                $sql = File::get(base_path($request['path'] . 'database/addon/payment_requests.sql'));
                DB::unprepared($sql);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return back();
        }

        //version 7.2
        DB::table('business_settings')->updateOrInsert(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => 0,
                'web_api_key' => '',
            ]),
        ]);

        return redirect('/admin/auth/login');
    }

    private function set_payment_data(){
        try{
            $gateway= [
                'ssl_commerz_payment',
                'razor_pay',
                'paypal',
                'stripe',
                'senang_pay',
                'paystack',
                'bkash',
                'paymob',
                'flutterwave',
                'mercadopago',
            ];


            $data= BusinessSetting::whereIn('key',$gateway)->pluck('value','key')->toArray();

            foreach($data as $key => $value){
                $gateway=$key;
                if($key == 'ssl_commerz_payment' ){
                    $gateway='ssl_commerz';
                }
                if($key == 'paymob' ){
                    $gateway='paymob_accept';
                }

                $decoded_value= json_decode($value , true);
                $data= [
                    'gateway' => $gateway ,
                    'mode' =>  isset($decoded_value['status']) == 1  ?  'live': 'test'
                ];

                $additional_data =[];

                if ($gateway == 'ssl_commerz') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'store_id' => $decoded_value['store_id'],
                        'store_password' => $decoded_value['store_password'],
                    ];
                } elseif ($gateway == 'paypal') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'client_id' => $decoded_value['paypal_client_id'],
                        'client_secret' => $decoded_value['paypal_secret'],
                    ];
                } elseif ($gateway == 'stripe') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['api_key'],
                        'published_key' => $decoded_value['published_key'],
                    ];
                } elseif ($gateway == 'razor_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['razor_key'],
                        'api_secret' => $decoded_value['razor_secret'],
                    ];
                } elseif ($gateway == 'senang_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'secret_key' => $decoded_value['secret_key'],
                        'merchant_id' => $decoded_value['merchant_id'],
                    ];
                } elseif ($gateway == 'paystack') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => $decoded_value['paymentUrl'],
                        'public_key' => $decoded_value['publicKey'],
                        'secret_key' => $decoded_value['secretKey'],
                        'merchant_email' => $decoded_value['merchantEmail'],
                    ];
                } elseif ($gateway == 'paymob_accept') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'api_key' => $decoded_value['api_key'],
                        'iframe_id' => $decoded_value['iframe_id'],
                        'integration_id' => $decoded_value['integration_id'],
                        'hmac' => $decoded_value['hmac'],
                    ];
                } elseif ($gateway == 'mercadopago') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'access_token' => $decoded_value['access_token'],
                        'public_key' => $decoded_value['public_key'],
                    ];
                } elseif ($gateway == 'flutterwave') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'secret_key' => $decoded_value['secret_key'],
                        'public_key' => $decoded_value['public_key'],
                        'hash' => $decoded_value['hash'],
                    ];
                } elseif ($gateway == 'bkash') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'app_key' => $decoded_value['api_key'],
                        'app_secret' => $decoded_value['api_secret'],
                        'username' => $decoded_value['username'],
                        'password' => $decoded_value['password'],
                    ];
                }

                $credentials= json_encode(array_merge($data, $additional_data));

                $payment_additional_data=['gateway_title' => ucfirst(str_replace('_',' ',$gateway)),
                    'gateway_image' => null];

                DB::table('addon_settings')->updateOrInsert(['key_name' => $gateway, 'settings_type' => 'payment_config'], [
                    'key_name' => $gateway,
                    'live_values' => $credentials,
                    'test_values' => $credentials,
                    'settings_type' => 'payment_config',
                    'mode' => isset($decoded_value['status']) == 1  ?  'live': 'test',
                    'is_active' => isset($decoded_value['status']) == 1  ?  1: 0 ,
                    'additional_data' => json_encode($payment_additional_data),
                ]);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return true;
        }
        return true;
    }

    private function set_sms_data(){
        try{
            $sms_gateway= ['twilio_sms', 'nexmo_sms', 'msg91_sms', '2factor_sms'];

            $data= BusinessSetting::whereIn('key',$sms_gateway)->pluck('value','key')->toArray();
            foreach($data as $key => $value){
                $decoded_value= json_decode($value , true);

                if ($key == 'twilio_sms') {
                    $sms_gateway='twilio';
                    $additional_data = [
                        'status' => data_get($decoded_value,'status',null),
                        'sid' => data_get($decoded_value,'sid',null),
                        'messaging_service_sid' =>  data_get($decoded_value,'messaging_service_id',null),
                        'token' => data_get($decoded_value,'token',null),
                        'from' =>data_get($decoded_value,'from',null),
                        'otp_template' => data_get($decoded_value,'otp_template',null),
                    ];
                } elseif ($key == 'nexmo_sms') {
                    $sms_gateway='nexmo';
                    $additional_data = [
                        'status' => data_get($decoded_value,'status',null),
                        'api_key' => data_get($decoded_value,'api_key',null),
                        'api_secret' =>  data_get($decoded_value,'api_secret',null),
                        'token' => data_get($decoded_value,'token',null),
                        'from' =>  data_get($decoded_value,'from',null),
                        'otp_template' =>  data_get($decoded_value,'otp_template',null),
                    ];
                } elseif ($key == '2factor_sms') {
                    $sms_gateway='2factor';
                    $additional_data = [
                        'status' => data_get($decoded_value,'status',null),
                        'api_key' => data_get($decoded_value,'api_key',null),
                    ];
                } elseif ($key == 'msg91_sms') {
                    $sms_gateway='msg91';
                    $additional_data = [
                        'status' => data_get($decoded_value,'status',null),
                        'template_id' =>  data_get($decoded_value,'template_id',null),
                        'auth_key' =>  data_get($decoded_value,'authkey',null),
                    ];
                }elseif ($key == 'signalwire_sms') {
                    $sms_gateway='signal_wire';
                    $additional_data = [
                        'status' => data_get($decoded_value,'status',null),
                        'project_id' =>  data_get($decoded_value,'project_id',null),
                        'token' => data_get($decoded_value,'token',null),
                        'space_url' => data_get($decoded_value,'space_url',null),
                        'from' =>  data_get($decoded_value,'from',null),
                        'otp_template' =>  data_get($decoded_value,'otp_template',null),
                    ];
                }


                $data= [
                    'gateway' => $sms_gateway ,
                    'mode' =>  isset($decoded_value['status']) == 1  ?  'live': 'test'
                ];
                $credentials= json_encode(array_merge($data, $additional_data));

                DB::table('addon_settings')->updateOrInsert(['key_name' => $sms_gateway, 'settings_type' => 'sms_config'], [
                    'key_name' => $sms_gateway,
                    'live_values' => $credentials,
                    'test_values' => $credentials,
                    'settings_type' => 'sms_config',
                    'mode' => isset($decoded_value['status']) == 1  ?  'live': 'test',
                    'is_active' => isset($decoded_value['status']) == 1  ?  1: 0 ,
                ]);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return true;
        }
        return true;
    }
}

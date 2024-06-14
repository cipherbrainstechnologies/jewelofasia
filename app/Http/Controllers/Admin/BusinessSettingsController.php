<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\Translation;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use App\Model\TimeSlot;
use App\Models\AddonSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BusinessSettingsController extends Controller
{
    public function __construct(
        private BusinessSetting $business_settings
    ){}

    public function restaurant_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->business_settings->where(['key' => 'fav_icon'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        return view('admin-views.business-settings.restaurant-index');
    }

    public function delivery_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->business_settings->where(['key' => 'minimum_order_value'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1,
            ]);
        }

        return view('admin-views.business-settings.delivery-fee');
    }

    public function maintenance_mode(): \Illuminate\Http\JsonResponse
    {
        $mode = Helpers::get_business_settings('maintenance_mode');
        DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_mode'], [
            'value' => isset($mode) ? !$mode : 1
        ]);
        if (!$mode){
            return response()->json(['message' => translate('Maintenance Mode is On.')]);
        }
        return response()->json(['message' => translate('Maintenance Mode is Off.')]);
    }

    public function currency_symbol_position($side): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => 'Symbol position is ' . $side]);
    }

    public function phone_verification_status($status): \Illuminate\Http\JsonResponse
    {
        $email_status = DB::table('business_settings')->where('key','email_verification')->first()->value;

        if ($email_status == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'phone_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Phone verification status updated'
        ]);
    }

    public function email_verification_status($status): \Illuminate\Http\JsonResponse
    {
        $phone_status = DB::table('business_settings')->where('key','phone_verification')->first()->value;

        if ($phone_status == 1){
            return response()->json([
                'status' => 0,
                'message' => 'Both email and phone verification can not be active at a time!'
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'email_verification'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Email verification status updated'
        ]);
    }

    public function self_pickup_status($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'self_pickup'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Pickup status updated']);
    }

    public function dm_self_registration_status($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'dm_self_registration'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Delivery man self registration status updated']);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function guest_checkout_status($status): JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'guest_checkout'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'guest checkout status updated']);
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function partial_payment_status($status): JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'partial payment status updated']);
    }

    public function max_amount_status($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'maximum_amount_for_cod_order_status'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'status updated'
        ]);
    }
    public function free_delivery_status($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'free_delivery_over_amount_status'], [
            'value' => $status
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'status updated'
        ]);
    }

    public function restaurant_setup(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'country'], [
            'value' => $request['country']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
            'value' => $request['time_zone'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'time_format'], [
            'value' => $request['time_format']
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'currency'], [
            'value' => $request['currency'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'decimal_point_settings'], [
            'value' => $request['decimal_point_settings'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'footer_text'], [
            'value' => $request['footer_text'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'restaurant_name'], [
            'value' => $request->restaurant_name
        ]);

        $curr_logo = $this->business_settings->where(['key' => 'logo'])->first();
        if ($request->has('logo')) {
            $image_name = Helpers::update('restaurant/', $curr_logo->value, 'png', $request->file('logo'));
        } else {
            $image_name = $curr_logo['value'];
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'logo'], [
            'value' => $image_name,
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'email_address'], [
            'value' => $request['email'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'address'], [
            'value' => $request['address'],
        ]);



        DB::table('business_settings')->updateOrInsert(['key' => 'partial_payment_combine_with'], [
            'value' => $request['partial_payment_combine_with'],
        ]);

        $curr_fav_icon = $this->business_settings->where(['key' => 'fav_icon'])->first();
        DB::table('business_settings')->updateOrInsert(['key' => 'fav_icon'], [
            'value' => $request->has('fav_icon') ? Helpers::update('restaurant/', $curr_fav_icon->value, 'png', $request->file('fav_icon')) : $curr_fav_icon->value
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function mail_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.mail-index');
    }

    public function mail_send(Request $request): \Illuminate\Http\JsonResponse
    {
        $response_flag = 0;
        try {
            $emailServices = Helpers::get_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $response_flag = 1;
            }
        } catch (\Exception $exception) {
            $response_flag = 2;
        }

        return response()->json(['success' => $response_flag]);
    }

    public function mail_config(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = Helpers::get_business_settings('mail_config');

        $this->business_settings->where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => $data['status'],
                "name"       => $request['name'],
                "host"       => $request['host'],
                "driver"     => $request['driver'],
                "port"       => $request['port'],
                "username"   => $request['username'],
                "email_id"   => $request['email'],
                "encryption" => $request['encryption'],
                "password"   => $request['password'],
            ]),
        ]);
        Toastr::success(translate('Configuration updated successfully!'));

        return back();
    }

    public function mail_config_status($status): \Illuminate\Http\JsonResponse
    {
        $data = Helpers::get_business_settings('mail_config');
        $data['status'] = $status == '1' ? 1 : 0;

        $this->business_settings->where(['key' => 'mail_config'])->update([
            'value' => $data,
        ]);
        return response()->json(['message' => 'Mail config status updated']);
    }

    public function payment_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $routes = config('addon_admin_routes');
        $desiredName = 'payment_setup';
        $payment_url = '';

        foreach ($routes as $routeArray) {
            foreach ($routeArray as $route) {
                if ($route['name'] === $desiredName) {
                    $payment_url = $route['url'];
                    break 2;
                }
            }
        }

        $data_values = AddonSetting::whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paystack','paymob_accept','flutterwave','bkash','mercadopago'])
            ->get();

        return view('admin-views.business-settings.payment-index', compact('published_status', 'payment_url', 'data_values'));
    }

    public function payment_update(Request $request, $name): \Illuminate\Http\RedirectResponse
    {

        if ($name == 'cash_on_delivery') {
            $payment = $this->business_settings->where('key', 'cash_on_delivery')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'cash_on_delivery'])->update([
                    'key'        => 'cash_on_delivery',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'digital_payment') {
            $payment = $this->business_settings->where('key', 'digital_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'digital_payment'])->update([
                    'key'        => 'digital_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'offline_payment') {
            $payment = $this->business_settings->where('key', 'offline_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'offline_payment'])->update([
                    'key'        => 'offline_payment',
                    'value'      => json_encode([
                        'status' => $request['status'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }elseif ($name == 'ssl_commerz_payment') {
            $payment = $this->business_settings->where('key', 'ssl_commerz_payment')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => 1,
                        'store_id'       => '',
                        'store_password' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'ssl_commerz_payment'])->update([
                    'key'        => 'ssl_commerz_payment',
                    'value'      => json_encode([
                        'status'         => $request['status'],
                        'store_id'       => $request['store_id'],
                        'store_password' => $request['store_password'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'razor_pay') {
            $payment = $this->business_settings->where('key', 'razor_pay')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => 1,
                        'razor_key'    => '',
                        'razor_secret' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'razor_pay'])->update([
                    'key'        => 'razor_pay',
                    'value'      => json_encode([
                        'status'       => $request['status'],
                        'razor_key'    => $request['razor_key'],
                        'razor_secret' => $request['razor_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'paypal') {
            $payment = $this->business_settings->where('key', 'paypal')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => 1,
                        'paypal_client_id' => '',
                        'paypal_secret'    => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paypal'])->update([
                    'key'        => 'paypal',
                    'value'      => json_encode([
                        'status'           => $request['status'],
                        'paypal_client_id' => $request['paypal_client_id'],
                        'paypal_secret'    => $request['paypal_secret'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'stripe') {
            $payment = $this->business_settings->where('key', 'stripe')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => 1,
                        'api_key'       => '',
                        'published_key' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'stripe'])->update([
                    'key'        => 'stripe',
                    'value'      => json_encode([
                        'status'        => $request['status'],
                        'api_key'       => $request['api_key'],
                        'published_key' => $request['published_key'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($name == 'senang_pay') {
            $payment = $this->business_settings->where('key', 'senang_pay')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => 1,
                        'secret_key'  => '',
                        'merchant_id' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'senang_pay'])->update([
                    'key'        => 'senang_pay',
                    'value'      => json_encode([
                        'status'      => $request['status'],
                        'secret_key'  => $request['secret_key'],
                        'merchant_id' => $request['merchant_id'],
                    ]),
                    'updated_at' => now(),
                ]);
            }
        }elseif ($name == 'paystack') {
            $payment = $this->business_settings->where('key', 'paystack')->first();
            if (!isset($payment)) {
                DB::table('business_settings')->insert([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => 1,
                        'publicKey' => '',
                        'secretKey' => '',
                        'paymentUrl' => '',
                        'merchantEmail' => '',
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                DB::table('business_settings')->where(['key' => 'paystack'])->update([
                    'key' => 'paystack',
                    'value' => json_encode([
                        'status' => $request['status'],
                        'publicKey' => $request['publicKey'],
                        'secretKey' => $request['secretKey'],
                        'paymentUrl' => $request['paymentUrl'],
                        'merchantEmail' => $request['merchantEmail'],
                    ]),
                    'updated_at' => now()
                ]);
            }
        } else if ($name == 'bkash') {
            DB::table('business_settings')->updateOrInsert(['key' => 'bkash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'api_secret' => $request['api_secret'],
                    'username' => $request['username'],
                    'password' => $request['password'],
                ])
            ]);
        } else if ($name == 'paymob') {
            DB::table('business_settings')->updateOrInsert(['key' => 'paymob'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'iframe_id' => $request['iframe_id'],
                    'integration_id' => $request['integration_id'],
                    'hmac' => $request['hmac']
                ])
            ]);
        } else if ($name == 'flutterwave') {
            DB::table('business_settings')->updateOrInsert(['key' => 'flutterwave'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'hash' => $request['hash']
                ])
            ]);
        } else if ($name == 'mercadopago') {
            DB::table('business_settings')->updateOrInsert(['key' => 'mercadopago'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'access_token' => $request['access_token']
                ])
            ]);
        }else if ($name == '6cash') {
            DB::table('business_settings')->updateOrInsert(['key' => '6cash'], [
                'value' => json_encode([
                    'status' => $request['status'],
                    'public_key' => $request['public_key'],
                    'secret_key' => $request['secret_key'],
                    'merchant_number' => $request['merchant_number']
                ])
            ]);
        }

        Toastr::success(translate('payment settings updated!'));
        return back();
    }

    public function payment_config_update(Request $request)
    {
        $validation = [
            'gateway' => 'required|in:ssl_commerz,paypal,stripe,razor_pay,senang_pay,paystack,paymob_accept,flutterwave,bkash,mercadopago',
            'mode' => 'required|in:live,test'
        ];

        $request['status'] = $request->has('status') ? 1 : 0;

        $additional_data = [];

        if ($request['gateway'] == 'ssl_commerz') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'store_id' => 'required_if:status,1',
                'store_password' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paypal') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'client_id' => 'required_if:status,1',
                'client_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'published_key' => 'required_if:status,1',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'api_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'senang_pay') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_id' => 'required_if:status,1'
            ];
        }elseif ($request['gateway'] == 'paystack') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'public_key' => 'required_if:status,1',
                'secret_key' => 'required_if:status,1',
                'merchant_email' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'paymob_accept') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required_if:status,1',
                'api_key' => 'required_if:status,1',
                'iframe_id' => 'required_if:status,1',
                'integration_id' => 'required_if:status,1',
                'hmac' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'mercadopago') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'access_token' => 'required_if:status,1',
                'public_key' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'flutterwave') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'secret_key' => 'required_if:status,1',
                'public_key' => 'required_if:status,1',
                'hash' => 'required_if:status,1'
            ];
        }  elseif ($request['gateway'] == 'bkash') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'app_key' => 'required_if:status,1',
                'app_secret' => 'required_if:status,1',
                'username' => 'required_if:status,1',
                'password' => 'required_if:status,1',
            ];
        }

        $request->validate(array_merge($validation, $additional_data));

        $settings = AddonSetting::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();

        $additional_data_image = $settings['additional_data'] != null ? json_decode($settings['additional_data']) : null;

        if ($request->has('gateway_image')) {
            $gateway_image = Helpers::upload('payment_modules/gateway_image/', 'png', $request['gateway_image']);
        } else {
            $gateway_image = $additional_data_image != null ? $additional_data_image->gateway_image : '';
        }

        $payment_additional_data = [
            'gateway_title' => $request['gateway_title'],
            'gateway_image' => $gateway_image,
        ];

        $validator = Validator::make($request->all(), array_merge($validation, $additional_data));

        AddonSetting::updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'], [
            'key_name' => $request['gateway'],
            'live_values' => $validator->validate(),
            'test_values' => $validator->validate(),
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'is_active' => $request->status,
            'additional_data' => json_encode($payment_additional_data),
        ]);

        Toastr::success(GATEWAYS_DEFAULT_UPDATE_200['message']);
        return back();

    }


    public function currency_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.currency-index');
    }

    public function currency_store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        Currency::create([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency added successfully!'));
        return back();
    }

    public function currency_edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $currency = Currency::find($id);
        return view('admin-views.business-settings.currency-update', compact('currency'));
    }

    public function currency_update(Request $request, $id): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Currency::where(['id' => $id])->update([
            "country"         => $request['country'],
            "currency_code"   => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate"   => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    public function currency_delete($id): \Illuminate\Http\RedirectResponse
    {
        Currency::where(['id' => $id])->delete();
        Toastr::success(translate('Currency removed successfully!'));
        return back();
    }

    public function terms_and_conditions(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tnc = $this->business_settings->where(['key' => 'terms_and_conditions'])->first();
        if (!$tnc) {
            $this->business_settings->insert([
                'key'   => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('tnc'));
    }

    public function terms_and_conditions_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc,
        ]);
        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    public function privacy_policy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'privacy_policy'])->first();
        if (!$data) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }
        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    public function privacy_policy_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    public function about_us(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'about_us'])->first();
        if (!$data) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }
        return view('admin-views.business-settings.about-us', compact('data'));
    }

    public function about_us_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        Toastr::success(translate('About us updated!'));
        return back();
    }

    public function faq(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'faq'])->first();
        if (!$data) {
            $data = [
                'key' => 'faq',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }
        return view('admin-views.business-settings.faq', compact('data'));
    }

    public function faq_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'faq'])->update([
            'value' => $request->faq,
        ]);

        Toastr::success(translate('FAQ updated!'));
        return back();
    }

    public function cancellation_policy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'cancellation_policy'])->first();
        $status = $this->business_settings->where(['key' => 'cancellation_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'cancellation_policy',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'cancellation_policy_status',
                'value' => 0,
            ];
            $this->business_settings->insert($status);
        }
        return view('admin-views.business-settings.cancellation-policy', compact('data', 'status'));
    }

    public function cancellation_policy_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'cancellation_policy'])->update([
            'value' => $request->cancellation_policy,
        ]);

        Toastr::success(translate('Cancellation Policy updated!'));
        return back();
    }

    public function cancellation_policy_status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'cancellation_policy_status'])->update([
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }

    public function refund_policy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'refund_policy'])->first();
        $status = $this->business_settings->where(['key' => 'refund_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'refund_policy',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }
        if (!$status) {
            $status = [
                'key' => 'refund_policy_status',
                'value' => 0,
            ];
            $this->business_settings->insert($status);
        }
        return view('admin-views.business-settings.refund-policy', compact('data', 'status'));
    }

    public function refund_policy_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'refund_policy'])->update([
            'value' => $request->refund_policy,
        ]);

        Toastr::success(translate('Refund Policy updated!'));
        return back();
    }

    public function refund_policy_status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'refund_policy_status'])->update([
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }

    public function return_policy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = $this->business_settings->where(['key' => 'return_policy'])->first();
        $status = $this->business_settings->where(['key' => 'return_policy_status'])->first();
        if (!$data) {
            $data = [
                'key' => 'return_policy',
                'value' => '',
            ];
            $this->business_settings->insert($data);
        }

        if (!$status) {
            $status = [
                'key' => 'return_policy_status',
                'value' => 0,
            ];
            $this->business_settings->insert($status);
        }
        return view('admin-views.business-settings.return-policy', compact('data', 'status'));
    }

    public function return_policy_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'return_policy'])->update([
            'value' => $request->return_policy,
        ]);

        Toastr::success(translate('Return Policy updated!'));
        return back();
    }

    public function return_policy_status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->business_settings->where(['key' => 'return_policy_status'])->update([
            'value' => $request->status,
        ]);
        Toastr::success(translate('Status updated!'));
        return back();
    }


    /**
     * @return Application|Factory|View
     */
    public function fcm_index(): View|Factory|Application
    {
        if (!$this->business_settings->where(['key' => 'order_pending_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'order_pending_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'order_confirmation_msg'])->first()) {
            $this->business_settings->insert([
                'key'   => 'order_confirmation_msg',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'order_processing_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'order_processing_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'out_for_delivery_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'out_for_delivery_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'order_delivered_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'order_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'delivery_boy_assign_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'delivery_boy_assign_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'delivery_boy_start_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'delivery_boy_start_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'delivery_boy_delivered_message'])->first()) {
            $this->business_settings->insert([
                'key'   => 'delivery_boy_delivered_message',
                'value' => json_encode([
                    'status'  => 0,
                    'message' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'customer_notify_message'])->first()) {
            $this->business_settings->insert([
                'key' => 'customer_notify_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }
        if (!$this->business_settings->where(['key' => 'returned_message'])->first()) {
            $this->business_settings->insert([
                'key' => 'returned_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->business_settings->where(['key' => 'failed_message'])->first()) {
            $this->business_settings->insert([
                'key' => 'failed_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }if (!$this->business_settings->where(['key' => 'canceled_message'])->first()) {
            $this->business_settings->insert([
                'key' => 'canceled_message',
                'value' => json_encode([
                    'status' => 0,
                    'message' => '',
                ]),
            ]);
        }

        return view('admin-views.business-settings.fcm-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function fcm_config(): View|Factory|Application
    {
        if (!$this->business_settings->where(['key' => 'fcm_topic'])->first()) {
            $this->business_settings->insert([
                'key' => 'fcm_topic',
                'value' => '',
            ]);
        }
        if (!$this->business_settings->where(['key' => 'fcm_project_id'])->first()) {
            $this->business_settings->insert([
                'key' => 'fcm_project_id',
                'value' => '',
            ]);
        }
        if (!$this->business_settings->where(['key' => 'push_notification_key'])->first()) {
            $this->business_settings->insert([
                'key' => 'push_notification_key',
                'value' => '',
            ]);
        }

        return view('admin-views.business-settings.fcm-config');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_fcm(Request $request): RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'fcm_project_id'], [
            'value' => $request['fcm_project_id'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'push_notification_key'], [
            'value' => $request['push_notification_key'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    private function updateOrInsertMessage($business_key, $status_key ,$default_message_key, $multi_lang_message_key, $request)
    {
        $status = $request[$status_key] == 1 ? 1 : 0;
        $message = $request[$default_message_key];

        $this->business_settings->updateOrInsert(['key' => $business_key], [
            'value' => json_encode([
                'status' => $status,
                'message' => $message,
            ]),
        ]);

        $setting = $this->business_settings->where('key', $business_key)->first();

        foreach ($request->lang as $index => $lang) {
            if ($lang === 'default') {
                continue;
            }
            $messageValue = $request[$multi_lang_message_key][$index - 1] ?? null;
            if ($messageValue !== null) {
                Translation::updateOrInsert(
                    [
                        'translationable_type' => 'App\Model\BusinessSetting',
                        'translationable_id' => $setting->id,
                        'locale' => $lang,
                        'key' => $multi_lang_message_key,
                    ],
                    ['value' => $messageValue]
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_fcm_messages(Request $request): RedirectResponse
    {
        $this->updateOrInsertMessage('order_pending_message', 'pending_status','pending_message' ,'order_pending_message', $request);
        $this->updateOrInsertMessage('order_confirmation_msg', 'confirm_status','confirm_message' ,'order_confirmation_message', $request);
        $this->updateOrInsertMessage('order_processing_message', 'processing_status','processing_message' ,'order_processing_message', $request);
        $this->updateOrInsertMessage('out_for_delivery_message', 'out_for_delivery_status','out_for_delivery_message' ,'order_out_for_delivery_message', $request);
        $this->updateOrInsertMessage('order_delivered_message', 'delivered_status','delivered_message' ,'order_delivered_message', $request);
        $this->updateOrInsertMessage('delivery_boy_assign_message', 'delivery_boy_assign_status','delivery_boy_assign_message' ,'assign_deliveryman_message', $request);
        $this->updateOrInsertMessage('delivery_boy_start_message', 'delivery_boy_start_status','delivery_boy_start_message' ,'deliveryman_start_message', $request);
        $this->updateOrInsertMessage('delivery_boy_delivered_message', 'delivery_boy_delivered_status','delivery_boy_delivered_message' ,'deliveryman_delivered_message', $request);
        $this->updateOrInsertMessage('customer_notify_message', 'customer_notify_status','customer_notify_message' ,'customer_notification_message', $request);
        $this->updateOrInsertMessage('returned_message', 'returned_status','returned_message' ,'return_order_message', $request);
        $this->updateOrInsertMessage('failed_message', 'failed_status','failed_message' ,'failed_order_message', $request);
        $this->updateOrInsertMessage('canceled_message', 'canceled_status','canceled_message' ,'canceled_order_message', $request);
        $this->updateOrInsertMessage('deliveryman_order_processing_message', 'dm_order_processing_status','dm_order_processing_message' ,'deliveryman_order_processing_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_message', 'add_fund_status','add_fund_message' ,'add_fund_wallet_message', $request);
        $this->updateOrInsertMessage('add_fund_wallet_bonus_message', 'add_fund_bonus_status','add_fund_bonus_message' ,'add_fund_wallet_bonus_message', $request);

        Toastr::success(translate('Message updated!'));
        return back();
    }
    public function map_api_setting(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.map-api');
    }
    public function map_api_store(Request $request): \Illuminate\Http\RedirectResponse
    {
//        DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
//            'value' => $request['map_api_key'],
//        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'map_api_server_key'], [
            'value' => $request['map_api_server_key'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'map_api_client_key'], [
            'value' => $request['map_api_client_key'],
        ]);
        Toastr::success(translate('Map API updated successfully'));
        return back();
    }

    //recaptcha
    public function recaptcha_index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.recaptcha-index');
    }

    public function recaptcha_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => $request['status'],
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    public function app_setting_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return View('admin-views.business-settings.app-setting-index');
    }

    public function app_setting_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        if($request->platform == 'android')
        {
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if($request->platform == 'ios')
        {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);

            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }

        Toastr::error(translate('Updated failed'));
        return back();
    }

    public function firebase_message_config_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return View('admin-views.business-settings.firebase-config-index');
    }

    public function firebase_message_config(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'firebase_message_config'], [
            'key' => 'firebase_message_config',
            'value' => json_encode([
                'apiKey' => $request['apiKey'],
                'authDomain' => $request['authDomain'],
                'projectId' => $request['projectId'],
                'storageBucket' => $request['storageBucket'],
                'messagingSenderId' => $request['messagingSenderId'],
                'appId' => $request['appId'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        self::firebase_message_config_file_gen();

        Toastr::success(translate('Config Updated Successfully'));
        return back();
    }

    function firebase_message_config_file_gen()
    {
        //configs
        $config=\App\CentralLogics\Helpers::get_business_settings('firebase_message_config');
        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';

        try {
            $old_file = fopen("firebase-messaging-sw.js", "w") or die("Unable to open file!");

            $new_text = "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');\n";
            $new_text .= "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');\n";
            $new_text .= 'firebase.initializeApp({apiKey: "' . $apiKey . '",authDomain: "' . $authDomain . '",projectId: "' . $projectId . '",storageBucket: "' . $storageBucket . '", messagingSenderId: "' . $messagingSenderId . '", appId: "' . $appId . '"});';
            $new_text .= "\nconst messaging = firebase.messaging();\n";
            $new_text .= "messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });";
            $new_text .= "\n";

            fwrite($old_file, $new_text);
            fclose($old_file);

        }catch (\Exception $exception) {}

    }

    //social media

    /**
     * @return Application|Factory|View
     */
    public function social_media(): Factory|View|Application
    {
        return view('admin-views.business-settings.social-media');
    }

    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = SocialMedia::orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

    public function social_media_store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            SocialMedia::updateOrInsert([
                'name' => $request->get('name'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);

            return response()->json([
                'success' => 1,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => 1,
            ]);
        }

    }

    public function social_media_edit(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = SocialMedia::where('id', $request->id)->first();
        return response()->json($data);
    }

    public function social_media_update(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:social_medias',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['error' => 1]);
        }
        $request->validate([

        ]);

        $social_media = SocialMedia::find($request->id);
        $social_media->name = $request->name;
        $social_media->link = $request->link;
        $social_media->save();
        return response()->json();
    }

    public function social_media_delete(Request $request): \Illuminate\Http\JsonResponse
    {
        $br = SocialMedia::find($request->id);
        $br->delete();
        return response()->json();
    }

    public function social_media_status_update(Request $request): \Illuminate\Http\JsonResponse
    {
        SocialMedia::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function main_branch_setup(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $main_branch = Branch::where(['id' => 1])->first();
        return view('admin-views.business-settings.main-branch-setup', compact('main_branch'));
    }

    public function delivery_setup_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        if($request->delivery_charge == null) {
            $request->delivery_charge = $this->business_settings->where(['key' => 'delivery_charge'])->first()->value;
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'delivery_charge'], [
            'value' => $request->delivery_charge,
        ]);

        if ($request['shipping_status'] == 1) {
            $request->validate([
                'min_shipping_charge' => 'required',
                'shipping_per_km' => 'required',
            ],
                [
                    'min_shipping_charge.required' => translate('Minimum shipping charge is required while shipping method is active'),
                    'shipping_per_km.required' => translate('Shipping charge per Kilometer is required while shipping method is active'),
                ]);
        }
        if($request['min_shipping_charge'] == null) {
            $request['min_shipping_charge'] = Helpers::get_business_settings('delivery_management')['min_shipping_charge'];
        }
        if($request['shipping_per_km'] == null) {
            $request['shipping_per_km'] = Helpers::get_business_settings('delivery_management')['shipping_per_km'];
        }
        DB::table('business_settings')->updateOrInsert(['key' => 'delivery_management'], [
            'value' => json_encode([
                'status'  => $request['shipping_status'],
                'min_shipping_charge' => $request['min_shipping_charge'],
                'shipping_per_km' => $request['shipping_per_km'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'free_delivery_over_amount'], [
            'value' => $request['free_delivery_over_amount'],
        ]);

        Toastr::success(translate('Settings Updated'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function social_media_login(): Factory|View|Application
    {
        $apple = BusinessSetting::where('key', 'apple_login')->first();
        if (!$apple) {
            DB::table('business_settings')->updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":"","status":""}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $apple = BusinessSetting::where('key', 'apple_login')->first();
        }
        $appleLoginService = json_decode($apple->value, true);

        return view('admin-views.business-settings.social-media-login', compact('appleLoginService'));
    }

    public function google_social_login($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'google_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    public function facebook_social_login($status): \Illuminate\Http\JsonResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'facebook_social_login'], [
            'value' => $status
        ]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_apple_login(Request $request): RedirectResponse
    {
        $apple_login = Helpers::get_business_settings('apple_login');

        if ($request->hasFile('service_file')) {
            $fileName = Helpers::upload('apple-login/', 'p8', $request->file('service_file'));
        }

        $data = [
            'value' => json_encode([
                'login_medium' => 'apple',
                'client_id' => $request['client_id'],
                'client_secret' => '',
                'team_id' => $request['team_id'],
                'key_id' => $request['key_id'],
                'service_file' => $fileName ?? $apple_login['service_file'],
                'redirect_url' => '',
                'status' => $request->has('status') ? 1 : 0,
            ]),
        ];

        $this->business_settings->updateOrInsert(['key' => 'apple_login'], $data);

        Toastr::success(translate('settings updated!'));
        return back();
    }

    public function product_setup(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.product-setup-index');
    }

    public function product_setup_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'minimum_stock_limit'], [
            'value' => $request['minimum_stock_limit'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'product_vat_tax_status'], [
            'value' => $request['product_vat_tax_status'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'featured_product_status'], [
            'value' => $request['featured_product_status'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'trending_product_status'], [
            'value' => $request['trending_product_status'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'most_reviewed_product_status'], [
            'value' => $request['most_reviewed_product_status'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'recommended_product_status'], [
            'value' => $request['recommended_product_status'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function cookies_setup(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin-views.business-settings.cookies-setup-index');
    }

    public function cookies_setup_update(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $request['status'],
                'text' => $request['text'],
            ])
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function otp_setup(): Factory|View|Application
    {
        return view('admin-views.business-settings.otp-setup');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function otp_setup_update(Request $request): RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'maximum_otp_hit'], [
            'value' => $request['maximum_otp_hit'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'otp_resend_time'], [
            'value' => $request['otp_resend_time'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'temporary_block_time'], [
            'value' => $request['temporary_block_time'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'maximum_login_hit'], [
            'value' => $request['maximum_login_hit'],
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'temporary_login_block_time'], [
            'value' => $request['temporary_login_block_time'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function chat_index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->business_settings->where(['key' => 'whatsapp'])->first()) {
            $this->business_settings->insert([
                'key'   => 'whatsapp',
                'value' => json_encode([
                    'status'  => 0,
                    'number' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'telegram'])->first()) {
            $this->business_settings->insert([
                'key'   => 'telegram',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        if (!$this->business_settings->where(['key' => 'messenger'])->first()) {
            $this->business_settings->insert([
                'key'   => 'messenger',
                'value' => json_encode([
                    'status'  => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        return view('admin-views.business-settings.chat-index');
    }


    public function update_chat(Request $request): \Illuminate\Http\RedirectResponse
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'whatsapp'], [
            'value' => json_encode([
                'status'  => $request['whatsapp_status'] == 1 ? 1 : 0,
                'number' => $request['whatsapp_number'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'telegram'], [
            'value' => json_encode([
                'status'  => $request['telegram_status'] == 1 ? 1 : 0,
                'user_name' => $request['telegram_user_name'],
            ]),
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'messenger'], [
            'value' => json_encode([
                'status'  => $request['messenger_status'] == 1 ? 1 : 0,
                'user_name' => $request['messenger_user_name'],
            ]),
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function customer_setup(): Factory|View|Application
    {
        $data = $this->business_settings->where('key','like','wallet_%')
            ->orWhere('key','like','loyalty_%')
            ->orWhere('key','like','ref_earning_%')
            ->orWhere('key','like','add_fund_to_wallet%')
            ->orWhere('key','like','ref_earning_%')->get();
        $data = array_column($data->toArray(), 'value','key');

        return view('admin-views.business-settings.customer-setup', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function customer_setup_update(Request $request): RedirectResponse
    {
        $request->validate([
            'loyalty_point_exchange_rate'=>'nullable|numeric',
            'ref_earning_exchange_rate'=>'nullable|numeric',
            'loyalty_point_minimum_point'=>'numeric|min:0|not_in:0',
        ]);

        $this->business_settings->updateOrInsert(['key' => 'wallet_status'], [
            'value' => $request['customer_wallet']??0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'loyalty_point_status'], [
            'value' => $request['customer_loyalty_point']??0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'ref_earning_status'], [
            'value' => $request['ref_earning_status'] ?? 0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'loyalty_point_exchange_rate'], [
            'value' => $request['loyalty_point_exchange_rate'] ?? 0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'ref_earning_exchange_rate'], [
            'value' => $request['ref_earning_exchange_rate'] ?? 0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'loyalty_point_percent_on_item_purchase'], [
            'value' => $request['loyalty_point_percent_on_item_purchase']??0
        ]);
        $this->business_settings->updateOrInsert(['key' => 'loyalty_point_minimum_point'], [
            'value' => $request['minimun_transfer_point']??1
        ]);

        $this->business_settings->updateOrInsert(['key' => 'add_fund_to_wallet'], [
            'value' => $request['add_fund_to_wallet']??0
        ]);

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function order_setup(): Factory|View|Application
    {
        if (!$this->business_settings->where(['key' => 'minimum_order_value'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1,
            ]);
        }

        return view('admin-views.business-settings.order-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function order_setup_update(Request $request): RedirectResponse
    {
       $status = $request->maximum_amount_for_cod_order_status ? 1 : 0;
        DB::table('business_settings')->updateOrInsert(['key' => 'maximum_amount_for_cod_order_status'], [
            'value' => $status
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'maximum_amount_for_cod_order'], [
            'value' => $request['maximum_amount_for_cod_order'],
        ]);

        DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
            'value' => $request['minimum_order_value'],
        ]);

        Toastr::success(translate('customer_settings_updated_successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function firebase_otp_verification(): Factory|View|Application
    {
        return view('admin-views.business-settings.firebase-otp-verification');
    }
    public function firebase_otp_verification_update(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status'  => $request->has('status') ? 1 : 0,
                'web_api_key' => $request['web_api_key'],
            ]),
        ]);

        if ($request->has('status')){
            foreach (['twilio','nexmo','2factor','msg91', 'signal_wire'] as $gateway) {
                $keep = AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->first();
                if (isset($keep)) {
                    $hold = $keep->live_values;
                    $hold['status'] = 0;
                    AddonSetting::where(['key_name' => $gateway, 'settings_type' => 'sms_config'])->update([
                        'live_values' => $hold,
                        'test_values' => $hold,
                        'is_active' => 0,
                    ]);
                }
            }
        }

        Toastr::success(translate('updated_successfully'));
        return back();
    }
}

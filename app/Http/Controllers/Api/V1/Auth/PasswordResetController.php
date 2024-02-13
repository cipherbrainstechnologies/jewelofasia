<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\CentralLogics\SMS_module;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;
use Modules\Gateways\Traits\SmsGateway;

class PasswordResetController extends Controller
{
    public function __construct(
        private User $user,
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reset_password_request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $customer = $this->user->where(['email' => $request['email_or_phone']])
            ->orWhere('phone', 'like', "%{$request['email_or_phone']}%")->first();

        $send_by_phone = Helpers::get_business_settings('phone_verification');

        if (isset($customer)) {

            $otp_interval_time= Helpers::get_business_settings('otp_resend_time') ?? 60; // seconds
            $password_verification_data= DB::table('password_resets')->where('email_or_phone', $request['email_or_phone'])->first();

            if(isset($password_verification_data) &&  Carbon::parse($password_verification_data->created_at)->DiffInSeconds() < $otp_interval_time){
                $time= $otp_interval_time - Carbon::parse($password_verification_data->created_at)->DiffInSeconds();

                $errors = [];
                $errors[] = [
                    'code' => 'otp',
                    'message' => translate('please_try_again_after_') . $time . ' ' . translate('seconds')
                ];
                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            if(env('APP_MODE')=='live'){
                $token = rand(100000, 999999);
            }else{
                $token = 123456;
            }

            DB::table('password_resets')->updateOrInsert(['email_or_phone' => $request['email_or_phone']], [
                'email_or_phone' => $request['email_or_phone'],
                'token' => $token,
                'created_at' => now(),
            ]);


            if ($send_by_phone) {
                if(addon_published_status('Gateways')){
                    $response = SmsGateway::send($request['phone'],$token);
                }else{
                    $response = SMS_module::send($request['phone'], $token);
                }
                return response()->json([
                    'message' => $response
                ], 200);
            }

            try {
                $emailServices = Helpers::get_business_settings('mail_config');

                if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                    $name = $customer->f_name. ' '. $customer->l_name;
                    Mail::to($customer['email'])->send(new \App\Mail\Customer\PasswordReset($token, $name));
                }
            } catch (\Exception $exception) {
                return response()->json(['errors' => [
                    ['code' => 'config-missing', 'message' => 'Email configuration issue.']
                ]], 400);
            }

            return response()->json(['message' => 'Email sent successfully.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'Customer not found!']
        ]], 404);
    }

    public function verify_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'reset_token'=> 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $max_otp_hit = Helpers::get_business_settings('maximum_otp_hit') ?? 5;
        $max_otp_hit_time = Helpers::get_business_settings('otp_resend_time') ?? 60;    // seconds
        $temp_block_time = Helpers::get_business_settings('temporary_block_time') ?? 600;   // seconds

        $verify = DB::table('password_resets')->where(['token' => $request['reset_token'], 'email_or_phone' => $request['email_or_phone']])->first();
        if (isset($verify)) {
            if(isset($verify->temp_block_time ) && Carbon::parse($verify->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                $time = $temp_block_time - Carbon::parse($verify->temp_block_time)->DiffInSeconds();

                $errors = [];
                $errors[] = ['code' => 'otp_block_time',
                    'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];
                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            return response()->json(['message' => "Token found, you can proceed"], 200);

        }
        else{

            $verification_data= DB::table('password_resets')->where('email_or_phone', $request['email_or_phone'])->first();

            if(isset($verification_data)){
                $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                if(isset($verification_data->temp_block_time ) && Carbon::parse($verification_data->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors[] = [
                        'code' => 'otp_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                if($verification_data->is_temp_blocked == 1 && Carbon::parse($verification_data->created_at)->DiffInSeconds() >= $max_otp_hit_time){
                    DB::table('password_resets')->updateOrInsert(['email_or_phone' => $request['email_or_phone']],
                        [
                            'otp_hit_count' => 0,
                            'is_temp_blocked' => 0,
                            'temp_block_time' => null,
                            'created_at' => now(),
                        ]);
                }

                if($verification_data->otp_hit_count >= $max_otp_hit &&  Carbon::parse($verification_data->created_at)->DiffInSeconds() < $max_otp_hit_time &&  $verification_data->is_temp_blocked == 0){
                    DB::table('password_resets')->updateOrInsert(['email_or_phone' => $request['email_or_phone']],
                        [
                            'is_temp_blocked' => 1,
                            'temp_block_time' => now(),
                            'created_at' => now(),
                        ]);

                    $errors = [];
                    $errors[] = [
                        'code' => 'otp_temp_blocked',
                        'message' => translate('Too_many_attempts. please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 405);
                }

            }

            DB::table('password_resets')->updateOrInsert(['email_or_phone' => $request['email_or_phone']],
                [
                    'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                    'created_at' => now(),
                    'temp_block_time' => null,
                ]);
        }


        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'OTP is not matched']
        ]], 400);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'reset_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = DB::table('password_resets')->where(['email_or_phone' => $request['email_or_phone']])
            ->where(['token' => $request['reset_token']])->first();

        if (isset($data)) {

            if ($request['password'] == $request['confirm_password']) {
                $customer = $this->user->where(['email' => $request['email_or_phone']])->orWhere('phone', $request['email_or_phone'])->first();
                $customer->password = bcrypt($request['confirm_password']);
                $customer->save();

                DB::table('password_resets')
                    ->where(['email_or_phone' => $request['email_or_phone']])
                    ->where(['token' => $request['reset_token']])->delete();

                return response()->json(['message' => 'Password changed successfully.'], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'mismatch', 'message' => 'Password did not match!']
            ]], 401);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }
}

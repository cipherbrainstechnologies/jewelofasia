<?php

namespace App\Http\Controllers\Branch\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\RegisterDevice;
use Brian2694\Toastr\Facades\Toastr;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Facades\Agent;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:branch', ['except' => ['logout']]);
    }

    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if(Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        Session::put('default_captcha_code_branch', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    public function login()
    {
        return view('branch-views.auth.login');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $ip = $request->ip();
        $browser_name = Agent::browser();
        $browser_version = Agent::version($browser_name);
        $device_type = Agent::device();
        $device_platform = Agent::platform();

        //recaptcha validation
        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;
                        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                        $response = \file_get_contents($url);
                        $response = json_decode($response);
                        if (!$response->success) {
                            $fail(\App\CentralLogics\translate('ReCAPTCHA Failed'));
                        }
                    },
                ],
            ]);
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code_branch'))) {
                return back()->withErrors(\App\CentralLogics\translate('Captcha Failed'));
            }
        }

        if(Session::has('default_captcha_code_branch')) {
            Session::forget('default_captcha_code_branch');
        }
        //end recaptcha validation

        if (auth('branch')->attempt(['email' => $request->email, 'password' => $request->password, 'status' => 1], $request->remember)) {
            $unique_identifier = md5($ip . $browser_name . $browser_version . $device_type . $device_platform);

            $registered_device = RegisterDevice::where(['unique_identifier' => $unique_identifier, 'user_type' => 'branch'])->first();
            if (!isset($registered_device)){
                $register_device = new RegisterDevice();
                $register_device->user_id = auth('branch')->user()->id;
                $register_device->user_type = 'branch';
                $register_device->ip_address = $ip;
                $register_device->browser_name = $browser_name;
                $register_device->browser_version = $browser_version;
                $register_device->device_type = $device_type;
                $register_device->device_platform = $device_platform;
                $register_device->is_robot = Agent::isRobot() ? 1 : 0;
                $register_device->unique_identifier = $unique_identifier;
                $register_device->save();

                try {
                    $emailServices = Helpers::get_business_settings('mail_config');
                    if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                        Mail::to(auth('branch')->user()->email)->send(new \App\Mail\Branch\LoginAlert($request->ip(), auth('branch')->user()->name));
                    }
                } catch (\Exception $e) {
                }

            }

            return redirect()->route('branch.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }

    public function logout(Request $request)
    {
        auth()->guard('branch')->logout();
        return redirect()->route('branch.auth.login');
    }
}

<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $mode = env('APP_MODE');

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            if ($emailServices) {
                $config = array(
                    'driver' => $emailServices['driver'],
                    'host' => $emailServices['host'],
                    'port' => $emailServices['port'],
                    'username' => $emailServices['username'],
                    'password' => $emailServices['password'],
                    'encryption' => $emailServices['encryption'],
                    'from' => array('address' => $emailServices['email_id'], 'name' => $emailServices['name']),
                    'sendmail' => '/usr/sbin/sendmail -bs',
                    'pretend' => false,
                );
                Config::set('mail', $config);
            }


            $timezone = Helpers::get_business_settings('timezone');
            if ($timezone) {
                Config::set('timezone', $timezone->value);
                date_default_timezone_set($timezone->value);
            }

            //time format setup
            $time_format = Helpers::get_business_settings('time_format');
            if ($time_format && $time_format == '12') {
                Config::set('time_format', 'h:i:s A');
            }
            else{
                Config::set('time_format', 'H:i:s');
            }

        } catch (\Exception $ex) {
        }
    }
}

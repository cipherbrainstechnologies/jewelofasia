<?php

namespace App\Traits;

use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;

trait HelperTrait
{
    public function translate_message($language_code, $message_key)
    {
        $message = '';
        $status_key = Helpers::order_status_message_key($message_key);
        $translated_message = BusinessSetting::with('translations')->where(['key' => $status_key])->first();
        if (isset($translated_message->translations)){
            foreach ($translated_message->translations as $translation){
                if ($language_code == $translation->locale){
                    $message = $translation->value;
                }
            }
        }
        return $message;
    }

    public function dynamic_key_replaced_message($message, $type, $order = null, $customer = null)
    {
        $customer_name = '';
        $delivery_man_name = '';
        $order_id = $order ? $order->id : '';

        if ($type == 'order'){
            $delivery_man_name = $order->delivery_man ? $order->delivery_man->f_name. ' '. $order->delivery_man->l_name : '';
            $customer_name = $order->is_guest == 0 ? ($order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '') : 'Guest User';
        }
        if ($type == 'wallet'){
            $customer_name = $customer->f_name. ' '. $customer->l_name;
        }
        $store_name = Helpers::get_business_settings('restaurant_name');
        $value = Helpers::text_variable_data_format(value:$message, user_name: $customer_name, store_name: $store_name, delivery_man_name: $delivery_man_name, order_id: $order_id);
        return $value;
    }


}

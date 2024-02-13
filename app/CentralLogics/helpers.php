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

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        if (empty(json_decode($variation, true))) {
            $result = $product['price'];
        } else {
            $match = json_decode($variation, true)[0];
            $result = 0;
            foreach (json_decode($product['variations'], true) as $property => $value) {
                if ($value['type'] == $match['type']) {
                    $result = $value['price'];
                }
            }
        }
        return self::set_price($result);
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                $item['category_ids'] = json_decode($item['category_ids']);
                $item['image'] = json_decode($item['image']);
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);

                $categories = gettype($item['category_ids']) == 'array' ? $item['category_ids'] : json_decode($item['category_ids']);
                if(!is_null($categories) && count($categories) > 0) {
                    $ids = [];
                    foreach ($categories as $value) {
                        if ($value->position == 1) {
                            $ids[] = $value->id;
                        }
                    }
                    $item['category_discount']= CategoryDiscount::active()->where('category_id', $ids)->first();
                } else {
                    $item['category_discount'] = [];
                }

                foreach (json_decode($item['variations'], true) as $var) {
                    $variations[] = [
                        'type' => $var['type'],
                        'price' => (float)$var['price'],
                        'stock' => isset($var['stock']) ? (integer)$var['stock'] : (integer)0,
                    ];
                }
                $item['variations'] = $variations;

                if (count($item['translations'])) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation->key == 'name') {
                            $item['name'] = $translation->value;
                        }
                        if ($translation->key == 'description') {
                            $item['description'] = $translation->value;
                        }
                    }
                }
                unset($item['translations']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
//            $data['category_ids'] = json_decode($data['category_ids']);
//            $data['image'] = json_decode($data['image']);
//            $data['attributes'] = json_decode($data['attributes']);
//            $data['choice_options'] = json_decode($data['choice_options']);

            $data['category_ids'] = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids']);
            $data['image'] =  gettype($data['image']) == 'array' ? $data['image'] : json_decode($data['image']);
            $data['attributes'] = gettype($data['attributes']) == 'array' ? $data['attributes'] : json_decode($data['attributes']);
            $data['choice_options'] = gettype($data['choice_options']) == 'array' ? $data['choice_options'] : json_decode($data['choice_options']);

            $categories = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids'], true);

            if(!is_null($categories) && count($categories) > 0) {
                $ids = [];
                foreach ($categories as $value) {
                    $value = (array)$value;
                    if ($value['position'] == 1) {
                        $ids[] = $value['id'];
                    }
                }
                $data['category_discount']= CategoryDiscount::active()->where('category_id', $ids)->first();
            } else {
                $data['category_discount'] = [];
            }

//            foreach (json_decode($data['variations'], true) as $var) {
//                $variations[] = [
//                    'type' => $var['type'],
//                    'price' => (float)$var['price'],
//                    'stock' => isset($var['stock']) ? (integer)$var['stock'] : (integer)0,
//                ];
//            }
//            $data['variations'] = $variations;
            $data['variations'] = gettype($data['variations']) == 'array' ? $data['variations'] : json_decode($data['variations']);

            if (count($data['translations']) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation->key == 'name') {
                        $data['name'] = $translation->value;
                    }
                    if ($translation->key == 'description') {
                        $data['description'] = $translation->value;
                    }
                }
            }
        }

        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $data = \App\Model\BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function set_symbol($amount)
    {
        $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');
        $position = Helpers::get_business_settings('currency_symbol_position');
        if (!is_null($position) && $position == 'left') {
            $string = self::currency_symbol() . '' . number_format($amount, $decimal_point_settings);
        } else {
            $string = number_format($amount, $decimal_point_settings) . '' . self::currency_symbol();
        }
        return $string;
    }

    public static function set_price($amount)
    {
        $decimal_point_settings = Helpers::get_business_settings('decimal_point_settings');
        $amount = number_format($amount, $decimal_point_settings, '.', '');

        return $amount;
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "mutable-content": "true",
            "data" : {
                "title":"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0
              },
             "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function send_push_notif_to_topic($data)
    {
        /*https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send*/
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        /*$topic = BusinessSetting::where(['key' => 'fcm_topic'])->first()->value;*/
        /*$project_id = BusinessSetting::where(['key' => 'fcm_project_id'])->first()->value;*/

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $image = asset('storage/app/public/notification') . '/' . $data['image'];
        $postdata = '{
            "to" : "/topics/grofresh",
            "mutable-content": "true",
            "data" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $image . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0
              },
              "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $image . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "default"
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return self::set_price($price_discount);
    }

    public static function category_discount_calculate($category_id, $price)
    {
        $category_discount = CategoryDiscount::active()->where(['category_id' => $category_id])->first();
        if ($category_discount){
            if ($category_discount['discount_type'] == 'percent') {
                $price_discount = ($price / 100) * $category_discount['discount_amount'];
                if ($category_discount['maximum_amount'] < $price_discount) {
                    $price_discount = $category_discount['maximum_amount'];
                }
            } else {
                $price_discount = $category_discount['discount_amount'];
            }
        }else{
            $price_discount = 0;
        }
        return self::set_price($price_discount);
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = self::get_business_settings('order_pending_message');
        } elseif ($status == 'confirmed') {
            $data = self::get_business_settings('order_confirmation_msg');
        } elseif ($status == 'processing') {
            $data = self::get_business_settings('order_processing_message');
        } elseif ($status == 'out_for_delivery') {
            $data = self::get_business_settings('out_for_delivery_message');
        } elseif ($status == 'delivered') {
            $data = self::get_business_settings('order_delivered_message');
        } elseif ($status == 'delivery_boy_delivered') {
            $data = self::get_business_settings('delivery_boy_delivered_message');
        } elseif ($status == 'del_assign') {
            $data = self::get_business_settings('delivery_boy_assign_message');
        } elseif ($status == 'ord_start') {
            $data = self::get_business_settings('delivery_boy_start_message');
        } elseif ($status == 'returned') {
            $data = self::get_business_settings('returned_message');
        }  elseif ($status == 'failed') {
            $data = self::get_business_settings('failed_message');
        }  elseif ($status == 'canceled') {
            $data = self::get_business_settings('canceled_message');
        } elseif ($status == 'customer_notify_message') {
            $data = self::get_business_settings('customer_notify_message');
        } elseif ($status == 'deliveryman_order_processing') {
            $data = self::get_business_settings('deliveryman_order_processing_message');
        } elseif ($status == 'add_fund_wallet') {
            $data = self::get_business_settings('add_fund_wallet_message');
        } elseif ($status == 'add_fund_wallet_bonus') {
            $data = self::get_business_settings('add_fund_wallet_bonus_message');
        } else {
            $data = '{"status":"0","message":""}';
        }

        if ($data == null || $data['status'] == 0) {
            return 0;
        }
        return $data['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        Helpers::remove_dir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }

                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function get_default_language()
    {
        $data = self::get_business_settings('language');
        $default_lang = 'en';
        if($data && array_key_exists('code', $data)) {
            foreach ($data as $lang) {
                if($lang['default'] == true) {
                    $default_lang = $lang['code'];
                }
            }
        }

        return $default_lang;
    }

    public static function upload(string $dir, string $format, $image = null)
    {
        if ($image != null) {
            $imageName = \Carbon\Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (is_bool(env($envKey))) {
            $oldValue = var_export(env($envKey), true);
        } else {
            $oldValue = env($envKey);
        }

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);

        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender($request):array
    {
        $remove = array("http://", "https://", "www.");
        $url = str_replace($remove, "", url('/'));

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'],//un
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_key'],//pk
            base64_decode('c29mdHdhcmVfaWQ=') => base64_decode(env(base64_decode('U09GVFdBUkVfSUQ='))),//sid
            base64_decode('ZG9tYWlu') => $url,
        ];

        //session()->put('domain', 'https://' . preg_replace("#^[^:/.]*[:/]+#i", "", $request['domain']));

        $ch = curl_init('https://check.6amtech.com/api/v1/domain-register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);

        try {
            if (base64_decode(json_decode($response, true)['active'])) {
                return [
                    'active' => (int)base64_decode(json_decode($response, true)['active'])
                ];
            }
            return [
                'active' => 0
            ];
        } catch (\Exception $exception) {
            return [
                'active' => 1
            ];
        }
    }

    public static function getPagination()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit->value;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    public static function get_delivery_charge($distance)
    {
        $config = self::get_business_settings('delivery_management');

        if ($config['status'] != 1) {
            $delivery_charge = BusinessSetting::where(['key' => 'delivery_charge'])->first()->value;
            return $delivery_charge;
        } else {
            $delivery_charge = 0;

            $minimum_shipping_charge = $config['min_shipping_charge'];
            $shipping_charge = $config['shipping_per_km'];

            $delivery_charge = $shipping_charge * $distance;

            if ($delivery_charge > $minimum_shipping_charge) {
                return self::set_price($delivery_charge);
            } else {
                return self::set_price($minimum_shipping_charge);
            }
        }
    }

    public static function module_permission_check($mod_name)
    {
        $permission = auth('admin')->user()->role->module_access;

        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->admin_role_id == 1) {
            return true;
        }
        return false;
    }

    public static function file_remover(string $dir, $image)
    {
        if (!isset($image)) return true;

        if (Storage::disk('public')->exists($dir . $image)) Storage::disk('public')->delete($dir . $image);

        return true;
    }

    public static function generate_referer_code() {
        $ref_code = Str::random('20');
        if (User::where('referral_code', '=', $ref_code)->exists()) {
            return generate_referer_code();
        }
        return $ref_code;
    }

    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    public static function text_variable_data_format($value,$user_name=null,$store_name=null,$delivery_man_name=null,$transaction_id=null,$order_id=null)
    {
        $data = $value;
        if ($value) {
            if($user_name){
                $data =  str_replace("{userName}", $user_name, $data);
            }

            if($store_name){
                $data =  str_replace("{storeName}", $store_name, $data);
            }

            if($delivery_man_name){
                $data =  str_replace("{deliveryManName}", $delivery_man_name, $data);
            }

            if($order_id){
                $data =  str_replace("{orderId}", $order_id, $data);
            }
        }
        return $data;
    }

    public static function order_status_message_key($status)
    {
        if ($status == 'pending') {
            $data = 'order_pending_message';
        } elseif ($status == 'confirmed') {
            $data = 'order_confirmation_msg';
        } elseif ($status == 'processing') {
            $data = 'order_processing_message';
        } elseif ($status == 'out_for_delivery') {
            $data = 'out_for_delivery_message';
        } elseif ($status == 'delivered') {
            $data = 'order_delivered_message';
        } elseif ($status == 'delivery_boy_delivered') {
            $data = 'delivery_boy_delivered_message';
        } elseif ($status == 'del_assign') {
            $data = 'delivery_boy_assign_message';
        } elseif ($status == 'ord_start') {
            $data = 'delivery_boy_start_message';
        } elseif ($status == 'returned') {
            $data = 'returned_message';
        } elseif ($status == 'failed') {
            $data = 'failed_message';
        } elseif ($status == 'canceled') {
            $data = 'canceled_message';
        } elseif ($status == 'customer_notify_message') {
            $data = 'customer_notify_message';
        } elseif ($status == 'deliveryman_order_processing') {
            $data = 'deliveryman_order_processing_message';
        }elseif ($status == 'add_fund_wallet') {
            $data = 'add_fund_wallet_message';
        }elseif ($status == 'add_fund_wallet_bonus') {
            $data = 'add_fund_wallet_bonus_message';
        }  else {
            $data = $status;
        }

        return $data;
    }

}


function translate($key)
{
    $local = session()->has('local') ? session('local') : 'en';
    App::setLocale($local);

    try{
        $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));

        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $str = "<?php return " . var_export($lang_array, true) . ";";
            file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
    }catch(Exception $exception){
        $result = __('messages.' . $key);
    }

    return $result;
}



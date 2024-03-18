<?php

use App\Http\Controllers\Admin\AnalyticController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\System\AddonController;
use App\Http\Controllers\Admin\WalletBonusController;
use App\Http\Controllers\Admin\OfflinePaymentMethodController;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () {
    /*authentication*/
    Route::get('lang/{locale}', 'LanguageController@lang')->name('lang');

    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', 'LoginController@captcha')->name('default-captcha');
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['admin', 'employee_active_check']], function () {
            Route::get('/fcm/{id}', 'DashboardController@fcm')->name('dashboard');     //test route
            Route::get('/', 'DashboardController@dashboard')->name('dashboard');
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
            Route::get('settings', 'SystemController@settings')->name('settings');
            Route::post('settings', 'SystemController@settings_update');
            Route::post('settings-password', 'SystemController@settings_password_update')->name('settings-password');
            Route::get('/get-restaurant-data', 'SystemController@restaurant_data')->name('get-restaurant-data');
            Route::get('dashboard/order-statistics', 'DashboardController@get_order_statitics')->name('dashboard.order-statistics');
            Route::get('dashboard/earning-statistics', 'DashboardController@get_earning_statitics')->name('dashboard.earning-statistics');
        //});

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware'=>['module:user_management']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store')->name('store');
            Route::get('update/{id}', 'CustomRoleController@edit')->name('update');
            Route::post('update/{id}', 'CustomRoleController@update');
            Route::delete('delete/{id}', 'CustomRoleController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'CustomRoleController@status')->name('status');
            Route::get('export', 'CustomRoleController@export')->name('export');
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.','middleware'=>['module:user_management']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('update/{id}', 'EmployeeController@edit')->name('update');
            Route::post('update/{id}', 'EmployeeController@update');
            Route::get('status/{id}/{status}', 'EmployeeController@status')->name('status');
            Route::delete('delete/{id}', 'EmployeeController@delete')->name('delete');
            Route::get('export', 'EmployeeController@export')->name('export');
        });
        Route::group(['prefix' => 'pos', 'as' => 'pos.','middleware'=>['module:pos_management']], function () {
            Route::get('/', 'POSController@index')->name('index');
            Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
            Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
            Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
            Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
            Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
            Route::post('tax', 'POSController@update_tax')->name('tax');
            Route::post('discount', 'POSController@update_discount')->name('discount');
            Route::get('customers', 'POSController@get_customers')->name('customers');
            Route::post('order', 'POSController@place_order')->name('order');
            Route::get('orders', 'POSController@order_list')->name('orders');
            Route::get('order-details/{id}', 'POSController@order_details')->name('order-details');
            Route::get('invoice/{id}', 'POSController@generate_invoice');
            Route::any('store-keys', 'POSController@store_keys')->name('store-keys');
            Route::get('orders/export', 'POSController@export_orders')->name('orders.export');
            Route::post('customer.store', 'POSController@new_customer_store')->name('customer.store');
            Route::post('add-delivery-address', 'POSController@addDeliveryInfo')->name('add-delivery-address');
            Route::get('get-distance', 'POSController@get_distance')->name('get-distance');
            Route::post('order_type/store', 'POSController@order_type_store')->name('order_type.store');

        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.','middleware'=>['module:promotion_management']], function () {
            Route::get('add-new', 'BannerController@index')->name('add-new');
            Route::post('store', 'BannerController@store')->name('store');
            Route::get('edit/{id}', 'BannerController@edit')->name('edit');
            Route::put('update/{id}', 'BannerController@update')->name('update');
            Route::get('list', 'BannerController@list')->name('list');
            Route::get('status/{id}/{status}', 'BannerController@status')->name('status');
            Route::delete('delete/{id}', 'BannerController@delete')->name('delete');
        });

        Route::group(['prefix' => 'discount', 'as' => 'discount.','middleware'=>['module:promotion_management']], function () {
            Route::get('add-new', 'DiscountController@index')->name('add-new');
            Route::post('store', 'DiscountController@store')->name('store');
            Route::get('edit/{id}', 'DiscountController@edit')->name('edit');
            Route::post('update/{id}', 'DiscountController@update')->name('update');
            Route::get('list', 'DiscountController@list')->name('list');
            Route::get('status/{id}/{status}', 'DiscountController@status')->name('status');
            Route::delete('delete/{id}', 'DiscountController@delete')->name('delete');
        });

        Route::group(['prefix' => 'cities', 'as' => 'cities.'], function () {
            Route::get('add-new', 'CityController@index')->name('add-new');
            Route::post('store', 'CityController@store')->name('store');
            Route::get('edit/{id}', 'CityController@edit')->name('edit');
            Route::post('update/{id}', 'CityController@update')->name('update');
            Route::get('list', 'CityController@list')->name('list');
            Route::get('status/{id}/{status}', 'CityController@status')->name('status');
            Route::delete('delete/{id}', 'CityController@delete')->name('delete');
        });

        Route::group(['prefix' => 'zipcodes', 'as' => 'zipcodes.'], function () {
            Route::get('add-new', 'ZipcodeController@index')->name('add-new');
            Route::post('store', 'ZipcodeController@store')->name('store');
            Route::get('edit/{id}', 'ZipcodeController@edit')->name('edit');
            Route::post('update/{id}', 'ZipcodeController@update')->name('update');
            Route::get('list', 'ZipcodeController@list')->name('list');
            Route::get('status/{id}/{status}', 'ZipcodeController@status')->name('status');
            Route::delete('delete/{id}', 'ZipcodeController@delete')->name('delete');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.','middleware'=>['module:product_management']], function () {
            Route::get('add-new', 'AttributeController@index')->name('add-new');
            Route::post('store', 'AttributeController@store')->name('store');
            Route::get('edit/{id}', 'AttributeController@edit')->name('edit');
            Route::post('update/{id}', 'AttributeController@update')->name('update');
            Route::delete('delete/{id}', 'AttributeController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'AttributeController@status')->name('status');
        });

        Route::group(['prefix' => 'branch', 'as' => 'branch.','middleware'=>['module:system_management']], function () {
            Route::get('add-new', 'BranchController@index')->name('add-new');
            Route::get('list', 'BranchController@list')->name('list');
            Route::post('store', 'BranchController@store')->name('store');
            Route::get('edit/{id}', 'BranchController@edit')->name('edit');
            Route::post('update/{id}', 'BranchController@update')->name('update');
            Route::delete('delete/{id}', 'BranchController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'BranchController@status')->name('status');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.','middleware'=>['module:user_management']], function () {
            Route::get('add', 'DeliveryManController@index')->name('add');
            Route::post('store', 'DeliveryManController@store')->name('store');
            Route::get('list', 'DeliveryManController@list')->name('list');
            Route::get('preview/{id}', 'DeliveryManController@preview')->name('preview');
            Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
            Route::post('update/{id}', 'DeliveryManController@update')->name('update');
            Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
            Route::post('search', 'DeliveryManController@search')->name('search');
            Route::get('status/{id}/{status}', 'DeliveryManController@status')->name('status');
            Route::get('export', 'DeliveryManController@export')->name('export');
            Route::get('pending/list', 'DeliveryManController@pending_list')->name('pending');
            Route::get('denied/list', 'DeliveryManController@denied_list')->name('denied');
            Route::get('update-application/{id}/{status}', 'DeliveryManController@update_application')->name('application');

            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                Route::get('list', 'DeliveryManController@reviews_list')->name('list');
            });
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.','middleware'=>['module:promotion_management']], function () {
            Route::get('add-new', 'NotificationController@index')->name('add-new');
            Route::post('store', 'NotificationController@store')->name('store');
            Route::get('edit/{id}', 'NotificationController@edit')->name('edit');
            Route::post('update/{id}', 'NotificationController@update')->name('update');
            Route::get('status/{id}/{status}', 'NotificationController@status')->name('status');
            Route::delete('delete/{id}', 'NotificationController@delete')->name('delete');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.','middleware'=>['module:product_management']], function () {
            Route::get('add-new', 'ProductController@index')->name('add-new');
            Route::post('variant-combination', 'ProductController@variant_combination')->name('variant-combination');
            Route::post('store', 'ProductController@store')->name('store');
            Route::get('edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('update/{id}', 'ProductController@update')->name('update');
            Route::get('list', 'ProductController@list')->name('list');
            Route::delete('delete/{id}', 'ProductController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'ProductController@status')->name('status');
            Route::post('search', 'ProductController@search')->name('search');
            Route::get('bulk-import', 'ProductController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ProductController@bulk_import_data');
            Route::get('bulk-export-index', 'ProductController@bulk_export_index')->name('bulk-export-index');
            Route::get('bulk-export', 'ProductController@bulk_export_data')->name('bulk-export');

            Route::get('view/{id}', 'ProductController@view')->name('view');
            Route::get('remove-image/{id}/{name}', 'ProductController@remove_image')->name('remove-image');
            //ajax request
            Route::get('get-categories', 'ProductController@get_categories')->name('get-categories');
            Route::post('daily-needs', 'ProductController@daily_needs')->name('daily-needs');

            Route::get('limited-stock', 'ProductController@limited_stock')->name('limited-stock');
            Route::get('get-variations', 'ProductController@get_variations')->name('get-variations');
            Route::post('update-quantity', 'ProductController@update_quantity')->name('update-quantity');

            Route::get('feature/{id}/{is_featured}', 'ProductController@feature')->name('feature');

        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.','middleware'=>['module:order_management']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::get('status', 'OrderController@status')->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');
            Route::get('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice')->withoutMiddleware(['module:order_management']);
            Route::post('add-payment-ref-code/{id}', 'OrderController@add_payment_ref_code')->name('add-payment-ref-code');
            Route::get('branch-filter/{branch_id}', 'OrderController@branch_filter')->name('branch-filter');
            Route::post('date-search', 'OrderController@date_search')->name('date_search');
            Route::post('time-search', 'OrderController@time_search')->name('time_search');
            Route::post('search', 'OrderController@search')->name('search');
            Route::get('export/{status}', 'OrderController@export_orders')->name('export');
            Route::get('verify-offline-payment/{order_id}/{status}', 'OrderController@verify_offline_payment');
            Route::get('list/{status}', 'OrderController@list')->name('list');

        });

        Route::group(['prefix' => 'order', 'as' => 'order.','middleware'=>['module:order_management']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{id}', 'OrderController@update_shipping')->name('update-shipping');
            Route::post('update-timeSlot', 'OrderController@update_time_slot')->name('update-timeSlot');
            Route::post('update-deliveryDate', 'OrderController@update_deliveryDate')->name('update-deliveryDate');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.','middleware'=>['module:product_management']], function () {
            Route::get('add', 'CategoryController@index')->name('add');
            Route::get('add-sub-category', 'CategoryController@sub_index')->name('add-sub-category');
            Route::get('add-sub-sub-category', 'CategoryController@sub_sub_index')->name('add-sub-sub-category');
            Route::post('store', 'CategoryController@store')->name('store');
            Route::get('edit/{id}', 'CategoryController@edit')->name('edit');
            Route::post('update/{id}', 'CategoryController@update')->name('update');
            Route::post('store', 'CategoryController@store')->name('store');
            Route::get('status/{id}/{status}', 'CategoryController@status')->name('status');
            Route::delete('delete/{id}', 'CategoryController@delete')->name('delete');
            Route::post('search', 'CategoryController@search')->name('search');
            Route::get('priority', 'CategoryController@priority')->name('priority');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.','middleware'=>['module:support_management']], function () {
            Route::get('list', 'ConversationController@list')->name('list');
            Route::post('update-fcm-token', 'ConversationController@update_fcm_token')->name('update_fcm_token');
            Route::get('get-conversations', 'ConversationController@get_conversations')->name('get_conversations');
            Route::post('store/{user_id}', 'ConversationController@store')->name('store');
            Route::get('view/{user_id}', 'ConversationController@view')->name('view');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.','middleware'=>['module:user_management']], function () {
            Route::get('list', 'ReviewsController@list')->name('list');
            Route::post('search', 'ReviewsController@search')->name('search');
            Route::get('status/{id}/{status}', 'ReviewsController@status')->name('status');

        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.','middleware'=>['module:promotion_management']], function () {
            Route::get('add-new', 'CouponController@add_new')->name('add-new');
            Route::post('store', 'CouponController@store')->name('store');
            Route::get('update/{id}', 'CouponController@edit')->name('update');
            Route::post('update/{id}', 'CouponController@update');
            Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
            Route::delete('delete/{id}', 'CouponController@delete')->name('delete');
            Route::get('quick-view-details', 'CouponController@quick_view_details')->name('quick-view-details');

        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.','middleware'=>['module:system_management']], function () {

            Route::group(['prefix'=>'store','as'=>'store.'], function() {
                Route::get('ecom-setup', 'BusinessSettingsController@restaurant_index')->name('ecom-setup')->middleware('actch');
                Route::get('delivery-setup', 'BusinessSettingsController@delivery_index')->name('delivery-setup')->middleware('actch');
                Route::post('delivery-setup-update', 'BusinessSettingsController@delivery_setup_update')->name('delivery-setup-update');
                Route::post('update-setup', 'BusinessSettingsController@restaurant_setup')->name('update-setup');
                Route::get('maintenance-mode', 'BusinessSettingsController@maintenance_mode')->name('maintenance-mode');
                Route::get('currency-position/{position}', 'BusinessSettingsController@currency_symbol_position')->name('currency-position');
                Route::get('self-pickup/{status}', 'BusinessSettingsController@self_pickup_status')->name('self-pickup');
                Route::get('phone-verification/{status}', 'BusinessSettingsController@phone_verification_status')->name('phone-verification');
                Route::get('email-verification/{status}', 'BusinessSettingsController@email_verification_status')->name('email-verification');
                Route::get('location-setup', 'LocationSettingsController@location_index')->name('location-setup')->middleware('actch');
                Route::post('update-location', 'LocationSettingsController@location_setup')->name('update-location');
                Route::get('main-branch-setup', 'BusinessSettingsController@main_branch_setup')->name('main-branch-setup')->middleware('actch');
                Route::get('product-setup', 'BusinessSettingsController@product_setup')->name('product-setup');
                Route::post('product-setup-update', 'BusinessSettingsController@product_setup_update')->name('product-setup-update');
                Route::get('cookies-setup', 'BusinessSettingsController@cookies_setup')->name('cookies-setup');
                Route::post('cookies-setup-update', 'BusinessSettingsController@cookies_setup_update')->name('cookies-setup-update');
                Route::get('max-amount-status/{status}', 'BusinessSettingsController@max_amount_status')->name('max-amount-status');
                Route::get('free-delivery-status/{status}', 'BusinessSettingsController@free_delivery_status')->name('free-delivery-status');
                Route::get('dm-self-registration/{status}', 'BusinessSettingsController@dm_self_registration_status')->name('dm-self-registration');
                Route::get('otp-setup', 'BusinessSettingsController@otp_setup')->name('otp-setup');
                Route::post('otp-setup-update', 'BusinessSettingsController@otp_setup_update')->name('otp-setup-update');
                Route::get('guest-checkout/{status}', 'BusinessSettingsController@guest_checkout_status')->name('guest-checkout');
                Route::get('partial-payment/{status}', 'BusinessSettingsController@partial_payment_status')->name('partial-payment');
                Route::get('customer-setup', 'BusinessSettingsController@customer_setup')->name('customer-setup');
                Route::post('customer-setup-update', 'BusinessSettingsController@customer_setup_update')->name('customer-setup-update');
                Route::get('order-setup', 'BusinessSettingsController@order_setup')->name('order-setup');
                Route::post('order-setup-update', 'BusinessSettingsController@order_setup_update')->name('order-setup-update');


                Route::group(['prefix' => 'timeSlot', 'as' => 'timeSlot.'], function () {
                    Route::get('add-new', 'TimeSlotController@add_new')->name('add-new');
                    Route::post('store', 'TimeSlotController@store')->name('store');
                    Route::get('update/{id}', 'TimeSlotController@edit')->name('update');
                    Route::post('update/{id}', 'TimeSlotController@update');
                    Route::get('status/{id}/{status}', 'TimeSlotController@status')->name('status');
                    Route::delete('delete/{id}', 'TimeSlotController@delete')->name('delete');
                });
            });

            Route::group(['prefix'=>'web-app','as'=>'web-app.'], function() {
                Route::get('mail-config', 'BusinessSettingsController@mail_index')->name('mail-config')->middleware('actch');
                Route::post('mail-config', 'BusinessSettingsController@mail_config');
                Route::get('mail-config/status/{status}', 'BusinessSettingsController@mail_config_status')->name('mail-config.status');
                Route::post('mail-send', 'BusinessSettingsController@mail_send')->name('mail-send');

                Route::get('sms-module', 'SMSModuleController@sms_index')->name('sms-module');
                Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');

                Route::get('payment-method', 'BusinessSettingsController@payment_index')->name('payment-method')->middleware('actch');
                Route::post('payment-method-update/{payment_method}', 'BusinessSettingsController@payment_update')->name('payment-method-update');
                Route::post('payment-config-update', 'BusinessSettingsController@payment_config_update')->name('payment-config-update')->middleware('actch');


                Route::group(['prefix'=>'system-setup','as'=>'system-setup.'], function() {
                    //app settings
                    Route::get('app-setting', 'BusinessSettingsController@app_setting_index')->name('app_setting');
                    Route::post('app-setting', 'BusinessSettingsController@app_setting_update');

                    Route::get('db-index', 'DatabaseSettingsController@db_index')->name('db-index');
                    Route::post('db-clean', 'DatabaseSettingsController@clean_db')->name('clean-db');

                    Route::get('firebase-message-config', 'BusinessSettingsController@firebase_message_config_index')->name('firebase_message_config_index');
                    Route::post('firebase-message-config', 'BusinessSettingsController@firebase_message_config')->name('firebase_message_config');

                    //language
                    Route::group(['prefix' => 'language', 'as' => 'language.'], function () {
                        Route::get('', 'LanguageController@index')->name('index');
                        Route::post('add-new', 'LanguageController@store')->name('add-new');
                        Route::get('update-status', 'LanguageController@update_status')->name('update-status');
                        Route::get('update-default-status', 'LanguageController@update_default_status')->name('update-default-status');
                        Route::post('update', 'LanguageController@update')->name('update');
                        Route::get('translate/{lang}', 'LanguageController@translate')->name('translate');
                        Route::post('translate-submit/{lang}', 'LanguageController@translate_submit')->name('translate-submit');
                        Route::post('remove-key/{lang}', 'LanguageController@translate_key_remove')->name('remove-key');
                        Route::get('delete/{lang}', 'LanguageController@delete')->name('delete');
                    });
                });

                Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {
                    Route::get('map-api-settings','BusinessSettingsController@map_api_setting')->name('map-api-settings');
                    Route::post('map-api-store','BusinessSettingsController@map_api_store')->name('map-api-store');

                    //Social Icon
                    Route::get('social-media', 'BusinessSettingsController@social_media')->name('social-media');
                    Route::get('fetch', 'BusinessSettingsController@fetch')->name('fetch');
                    Route::post('social-media-store', 'BusinessSettingsController@social_media_store')->name('social-media-store');
                    Route::post('social-media-edit', 'BusinessSettingsController@social_media_edit')->name('social-media-edit');
                    Route::post('social-media-update', 'BusinessSettingsController@social_media_update')->name('social-media-update');
                    Route::post('social-media-delete', 'BusinessSettingsController@social_media_delete')->name('social-media-delete');
                    Route::post('social-media-status-update', 'BusinessSettingsController@social_media_status_update')->name('social-media-status-update');

                    Route::get('social-media-login', 'BusinessSettingsController@social_media_login')->name('social-media-login');
                    Route::get('google-social-login/{status}', 'BusinessSettingsController@google_social_login')->name('google-social-login');
                    Route::get('facebook-social-login/{status}', 'BusinessSettingsController@facebook_social_login')->name('facebook-social-login');
                    Route::post('update-apple-login', 'BusinessSettingsController@update_apple_login')->name('update-apple-login');


                    //recaptcha
                    Route::get('recaptcha', 'BusinessSettingsController@recaptcha_index')->name('recaptcha_index');
                    Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');

                    Route::get('fcm-index', 'BusinessSettingsController@fcm_index')->name('fcm-index')->middleware('actch');
                    Route::get('fcm-config', 'BusinessSettingsController@fcm_config')->name('fcm-config')->middleware('actch');
                    Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm')->middleware('actch');
                    Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages')->middleware('actch');

                    Route::get('chat-index', 'BusinessSettingsController@chat_index')->name('chat-index');
                    Route::post('update-chat', 'BusinessSettingsController@update_chat')->name('update-chat');

                    Route::get('firebase-otp-verification', 'BusinessSettingsController@firebase_otp_verification')->name('firebase-otp-verification');
                    Route::post('firebase-otp-verification-update', 'BusinessSettingsController@firebase_otp_verification_update')->name('firebase-otp-verification-update');


                    Route::group(['prefix' => 'offline-payment', 'as' => 'offline-payment.'], function(){
                        Route::get('list', [OfflinePaymentMethodController::class, 'list'])->name('list');
                        Route::get('add', [OfflinePaymentMethodController::class, 'add'])->name('add');
                        Route::post('store', [OfflinePaymentMethodController::class, 'store'])->name('store');
                        Route::get('edit/{id}', [OfflinePaymentMethodController::class, 'edit'])->name('edit');
                        Route::post('update/{id}', [OfflinePaymentMethodController::class, 'update'])->name('update');
                        Route::get('status/{id}/{status}', [OfflinePaymentMethodController::class, 'status'])->name('status');
                        Route::post('delete', [OfflinePaymentMethodController::class, 'delete'])->name('delete');
                    });
                });

            });

            Route::group(['prefix' => 'page-setup', 'as' => 'page-setup.'], function () {
                Route::get('terms-and-conditions', 'BusinessSettingsController@terms_and_conditions')->name('terms-and-conditions');
                Route::post('terms-and-conditions', 'BusinessSettingsController@terms_and_conditions_update');

                Route::get('privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy');
                Route::post('privacy-policy', 'BusinessSettingsController@privacy_policy_update');

                Route::get('about-us', 'BusinessSettingsController@about_us')->name('about-us');
                Route::post('about-us', 'BusinessSettingsController@about_us_update');

                Route::get('faq', 'BusinessSettingsController@faq')->name('faq');
                Route::post('faq', 'BusinessSettingsController@faq_update');

                Route::get('cancellation-policy', 'BusinessSettingsController@cancellation_policy')->name('cancellation-policy');
                Route::post('cancellation-policy', 'BusinessSettingsController@cancellation_policy_update');
                Route::get('cancellation-policy/status/{status}', 'BusinessSettingsController@cancellation_policy_status')->name('cancellation-policy.status');

                Route::get('refund-policy', 'BusinessSettingsController@refund_policy')->name('refund-policy');
                Route::post('refund-policy', 'BusinessSettingsController@refund_policy_update');
                Route::get('refund-policy/status/{status}', 'BusinessSettingsController@refund_policy_status')->name('refund-policy.status');

                Route::get('return-policy', 'BusinessSettingsController@return_policy')->name('return-policy');
                Route::post('return-policy', 'BusinessSettingsController@return_policy_update');
                Route::get('return-policy/status/{status}', 'BusinessSettingsController@return_policy_status')->name('return-policy.status');

            });

            Route::get('currency-add', 'BusinessSettingsController@currency_index')->name('currency-add')->middleware('actch');
            Route::post('currency-add', 'BusinessSettingsController@currency_store');
            Route::get('currency-update/{id}', 'BusinessSettingsController@currency_edit')->name('currency-update')->middleware('actch');
            Route::put('currency-update/{id}', 'BusinessSettingsController@currency_update');
            Route::delete('currency-delete/{id}', 'BusinessSettingsController@currency_delete')->name('currency-delete');

        });

        Route::group(['prefix' => 'report', 'as' => 'report.','middleware'=>['module:report_management']], function () {
            Route::get('order', 'ReportController@order_index')->name('order');
            Route::get('earning', 'ReportController@earning_index')->name('earning');
            Route::post('set-date', 'ReportController@set_date')->name('set-date');

            //Route::get('sale-report', 'ReportController@sale_report')->name('sale-report');
            //Route::post('sale-report-filter', 'ReportController@sale_filter')->name('sale-report-filter');
            //Route::get('sale-report', 'ReportController@sale_report_index')->name('sale-report');
            Route::get('sale-report', 'ReportController@new_sale_report')->name('sale-report');
            Route::get('export-sale-report', 'ReportController@export_sale_report')->name('export-sale-report');
            Route::get('expense', 'ReportController@expense_index')->name('expense');
            Route::get('expense-export-excel', 'ReportController@expense_export_excel')->name('expense.export.excel');
            Route::get('expense-export-pdf', 'ReportController@expense_summary_pdf')->name('expense.export.pdf');
        });

        Route::group(['prefix' => 'analytics', 'as' => 'analytics.','middleware'=>['module:report_management']], function () {
            Route::get('keyword-search', [AnalyticController::class, 'get_keyword_search'])->name('keyword-search');
            Route::get('customer-search', [AnalyticController::class, 'get_customer_search'])->name('customer-search');
            Route::get('keyword-export-excel', 'AnalyticController@export_keyword_search')->name('keyword.export.excel');
            Route::get('customer-export-excel', 'AnalyticController@export_customer_search')->name('customer.export.excel');

        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.','middleware'=>['module:user_management']], function () {
            Route::get('list', 'CustomerController@customer_list')->name('list');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::post('search', 'CustomerController@search')->name('search');
            Route::get('subscribed-emails', 'CustomerController@subscribed_emails')->name('subscribed_emails');
            Route::delete('delete/{id}', 'CustomerController@delete')->name('delete');
            Route::get('status/{id}/{status}', 'CustomerController@status')->name('status');
            Route::get('cancel-subscription/{id}', 'CustomerController@cancel_subscription')->name('cancel_subscription');
            Route::get('export', 'CustomerController@export_customer')->name('export');

            Route::get('select-list', 'CustomerWalletController@get_customers')->name('select-list');

            Route::group(['prefix' => 'wallet', 'as' => 'wallet.'], function () {
                Route::get('add-fund', 'CustomerWalletController@add_fund_view')->name('add-fund');
                Route::post('add-fund', 'CustomerWalletController@add_fund')->name('add-fund-store');
                Route::get('report', 'CustomerWalletController@report')->name('report');

                Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function () {
                    Route::get('index', [WalletBonusController::class, 'index'])->name('index');
                    Route::post('store',  [WalletBonusController::class, 'store'])->name('store');
                    Route::get('edit/{id}',  [WalletBonusController::class, 'edit'])->name('edit');
                    Route::post('update/{id}',  [WalletBonusController::class, 'update'])->name('update');
                    Route::get('status/{id}/{status}',  [WalletBonusController::class, 'status'])->name('status');
                    Route::delete('delete/{id}',  [WalletBonusController::class, 'delete'])->name('delete');
                });
            });

            Route::get('loyalty-point/report', 'LoyaltyPointController@report')->name('loyalty-point.report');
        });

        Route::group(['prefix' => 'offer', 'as' => 'offer.'], function () {
            Route::get('flash-index', 'OfferController@flash_index')->name('flash.index');
            Route::post('flash-store', 'OfferController@flash_store')->name('flash.store');
            Route::get('flash/edit/{id}', 'OfferController@flash_edit')->name('flash.edit');
            Route::post('flash/update/{id}', 'OfferController@flash_update')->name('flash.update');
            Route::get('flash/status/{id}/{status}', 'OfferController@status')->name('flash.status');
            Route::delete('flash/delete/{id}', 'OfferController@delete')->name('flash.delete');

            Route::get('flash/add-product/{flash_deal_id}', 'OfferController@flash_add_product')->name('flash.add-product');
            Route::post('flash/add-product/{flash_deal_id}', 'OfferController@flash_product_store')->name('flash.add-product.store');
            Route::post('flash/delete-product', 'OfferController@delete_flash_product')->name('flash.delete.product');
        });

        Route::group(['namespace' => 'System','prefix' => 'system-addon', 'as' => 'system-addon.'], function () {
            Route::get('/', [AddonController::class, 'index'])->name('index');
            Route::post('publish', [AddonController::class, 'publish'])->name('publish');
            Route::post('activation', [AddonController::class, 'activation'])->name('activation');
            Route::post('upload', [AddonController::class, 'upload'])->name('upload');
            Route::post('delete', [AddonController::class, 'delete_theme'])->name('delete');
        });


        Route::get('verify-offline-payment/quick-view-details', [OfflinePaymentMethodController::class, 'quick_view_details'])->name('offline-modal-view');
        Route::get('verify-offline-payment/{status}', [OfflinePaymentMethodController::class, 'offline_payment_list'])->name('verify-offline-payment');

    });
});

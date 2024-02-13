@extends('layouts.admin.app')

@section('title', translate('FCM Settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->

        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/firebase.png')}}" alt="">
                <span class="page-header-title ml-2 mt-2">
                    {{translate('firebase_push_notification_setup')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class=" card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 ml-3">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-index') }}" class="nav-link pb-2 px-0 pb-sm-3 active" data-slide="1">
                                <img src="{{asset('/public/assets/admin/img/notify.png')}}" alt="">
                                <span>{{translate('Push Notification')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-config') }}" class="nav-link pb-2 px-0 pb-sm-3" data-slide="2">
                                <img src="{{asset('/public/assets/admin/img/firebase2.png')}}" alt="">
                                <span>{{translate('Firebase Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="tab--content">
                            <div class="item show text-primary d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#push-notify-modal">
                                <strong class="mr-2">{{translate('Read Documentation')}}</strong>
                                <div class="blinkings">
                                    <i class="tio-info-outined"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active"id="push-notify">
                        @php($language = Helpers::get_business_settings('language'))
                        @php($default_lang = Helpers::get_default_language())

                        <form action="{{route('admin.business-settings.web-app.third-party.update-fcm-messages')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-8 mb-5">
                                    @if($language)
                                        <ul class="nav nav-tabs border-0">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active" href="#" id="default-link">{{ translate('Default') }}(EN)</a>
                                            </li>
                                            @foreach($language as $lang)
                                                <?php
                                                    if($lang['code'] == 'en')
                                                        continue;
                                                    ?>
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link" href="#" id="{{$lang['code']}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')'}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            <div class="lang_form" id="default-form">
                                <input type="hidden" name="lang[]" value="default">

                                <div class="row">
                                    @php($opm=\App\Model\BusinessSetting::with('translations')->where('key','order_pending_message')->first())
                                    @php($data=json_decode($opm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="pending_status">
                                                <input type="checkbox" name="pending_status" class="toggle-switch-input"
                                                       value="1" id="pending_status" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                            <span class="d-block">{{translate('order')}} {{translate('pending')}} {{translate('message')}}</span>
                                          </span>
                                            </label>
                                            <textarea name="pending_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($ocm=\App\Model\BusinessSetting::with('translations')->where('key','order_confirmation_msg')->first())
                                    @php($data=json_decode($ocm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="confirm_status">
                                                <input type="checkbox" name="confirm_status" class="toggle-switch-input"
                                                       value="1" id="confirm_status" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block"> {{translate('order')}} {{translate('confirmation')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="confirm_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($oprm=\App\Model\BusinessSetting::with('translations')->where('key','order_processing_message')->first())
                                    @php($data=json_decode($oprm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="processing_status">
                                                <input type="checkbox" name="processing_status"
                                                       class="toggle-switch-input"
                                                       value="1" id="processing_status" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block">{{translate('order')}} {{translate('processing')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="processing_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($ofdm=\App\Model\BusinessSetting::with('translations')->where('key','out_for_delivery_message')->first())
                                    @php($data=json_decode($ofdm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="out_for_delivery">
                                                <input type="checkbox" name="out_for_delivery_status"
                                                       class="toggle-switch-input"
                                                       value="1" id="out_for_delivery" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block">{{translate('order')}} {{translate('out_for_delivery')}} {{translate('message')}}</span>
                                              </span>
                                            </label>
                                            <textarea name="out_for_delivery_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($odm=\App\Model\BusinessSetting::with('translations')->where('key','order_delivered_message')->first())
                                    @php($data=json_decode($odm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="delivered_status">
                                                <input type="checkbox" name="delivered_status"
                                                       class="toggle-switch-input"
                                                       value="1" id="delivered_status" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block">{{translate('order')}} {{translate('delivered')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="delivered_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>
                                    @php($cnm=\App\Model\BusinessSetting::with('translations')->where('key','customer_notify_message')->first())
                                    @php($data=json_decode($cnm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">

                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="customer_notify">
                                                <input type="checkbox" name="customer_notify_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="customer_notify" {{isset($data) && $data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span
                                                    class="d-block">{{translate('DeliveryMan assign notification for customer')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="customer_notify_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($dba=\App\Model\BusinessSetting::with('translations')->where('key','delivery_boy_assign_message')->first())
                                    @php($data=json_decode($dba->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">

                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="delivery_boy_assign">
                                                <input type="checkbox" name="delivery_boy_assign_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="delivery_boy_assign" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block">{{translate('deliveryman')}} {{translate('assign')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="delivery_boy_assign_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($dbs=\App\Model\BusinessSetting::with('translations')->where('key','delivery_boy_start_message')->first())
                                    @php($data=json_decode($dbs->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="delivery_boy_start_status">
                                                <input type="checkbox" name="delivery_boy_start_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="delivery_boy_start_status" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block"> {{translate('deliveryman')}} {{translate('start')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="delivery_boy_start_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($dbc=\App\Model\BusinessSetting::with('translations')->where('key','delivery_boy_delivered_message')->first())
                                    @php($data=json_decode($dbc->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">

                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="delivery_boy_delivered">
                                                <input type="checkbox" name="delivery_boy_delivered_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="delivery_boy_delivered" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                                <span class="toggle-switch-content">
                                                <span class="d-block">{{translate('deliveryman')}} {{translate('delivered')}} {{translate('message')}}</span>
                                              </span>
                                            </label>

                                            <textarea name="delivery_boy_delivered_message" class="form-control">{{$data['message']}}</textarea>
                                        </div>
                                    </div>

                                    @php($orm=\App\Model\BusinessSetting::with('translations')->where('key','returned_message')->first())
                                    @php($data=json_decode($orm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="returned_status">
                                                <input type="checkbox" name="returned_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="returned_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('Order_returned_message')}}</span>
                                                  </span>
                                            </label>
                                            <textarea name="returned_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($ofm=\App\Model\BusinessSetting::with('translations')->where('key','failed_message')->first())
                                    @php($data=json_decode($ofm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="failed_status">
                                                <input type="checkbox" name="failed_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="failed_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('Order_failed_message')}}</span>
                                                  </span>
                                            </label>

                                            <textarea name="failed_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($ocanm=\App\Model\BusinessSetting::with('translations')->where('key','canceled_message')->first())
                                    @php($data=json_decode($ocanm->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="canceled_status">
                                                <input type="checkbox" name="canceled_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="canceled_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('Order_canceled_message')}}</span>
                                                  </span>
                                            </label>

                                            <textarea name="canceled_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($deliveryman_order_processing=\App\Model\BusinessSetting::with('translations')->where('key','deliveryman_order_processing_message')->first())
                                    @php($data=json_decode($deliveryman_order_processing->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="dm_order_processing_status">
                                                <input type="checkbox" name="dm_order_processing_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="dm_order_processing_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('deliveryman_order_processing_message')}}</span>
                                                  </span>
                                            </label>

                                            <textarea name="dm_order_processing_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($add_fund=\App\Model\BusinessSetting::with('translations')->where('key','add_fund_wallet_message')->first())
                                    @php($data=json_decode($add_fund->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="add_fund_status">
                                                <input type="checkbox" name="add_fund_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="add_fund_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('add_fund_wallet_message')}}</span>
                                                  </span>
                                            </label>

                                            <textarea name="add_fund_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    @php($add_fund_bonus=\App\Model\BusinessSetting::with('translations')->where('key','add_fund_wallet_bonus_message')->first())
                                    @php($data=json_decode($add_fund_bonus->value,true))
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3"
                                                   for="add_fund_bonus_status">
                                                <input type="checkbox" name="add_fund_bonus_status"
                                                       class="toggle-switch-input"
                                                       value="1"
                                                       id="add_fund_bonus_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                <span class="toggle-switch-content">
                                                    <span
                                                        class="d-block">{{translate('add_fund_wallet_bonus_message')}}</span>
                                                  </span>
                                            </label>

                                            <textarea name="add_fund_bonus_message" class="form-control">{{$data['message']??''}}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- MULTI LANG --}}
                            @if ($language)
                                @foreach($language as $lang)
                                        <?php
                                        if($lang['code'] == 'en')
                                            continue;
                                        ?>
                                    <div class="lang_form d-none" id="{{$lang['code']}}-form">
                                        <div class="row" >
                                            <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">

                                                <?php
                                                $notification_message_array = [
                                                    [
                                                        'field_name' => 'order_pending',
                                                        'object' => $opm
                                                    ],[
                                                        'field_name' => 'order_confirmation',
                                                        'object' => $ocm
                                                    ],[
                                                        'field_name' => 'order_processing',
                                                        'object' => $oprm
                                                    ],[
                                                        'field_name' => 'order_out_for_delivery',
                                                        'object' => $ofdm
                                                    ],[
                                                        'field_name' => 'order_delivered',
                                                        'object' => $odm
                                                    ],[
                                                        'field_name' => 'assign_deliveryman',
                                                        'object' => $dba
                                                    ], [
                                                        'field_name' => 'customer_notification',
                                                        'object' => $cnm
                                                    ], [
                                                        'field_name' => 'deliveryman_start',
                                                        'object' => $dbs
                                                    ],[
                                                        'field_name' => 'deliveryman_delivered',
                                                        'object' => $dbc
                                                    ], [
                                                        'field_name' => 'return_order',
                                                        'object' => $orm
                                                    ],[
                                                        'field_name' => 'failed_order',
                                                        'object' => $ofm
                                                    ],[
                                                        'field_name' => 'canceled_order',
                                                        'object' => $ocanm
                                                    ],[
                                                        'field_name' => 'deliveryman_order_processing',
                                                        'object' => $deliveryman_order_processing
                                                    ], [
                                                        'field_name' => 'add_fund_wallet',
                                                        'object' => $add_fund
                                                    ],[
                                                        'field_name' => 'add_fund_wallet_bonus',
                                                        'object' => $add_fund_bonus
                                                    ],

                                                ];

                                                $translation_holder = [];
                                                $translate = [];
                                                $temporary = [];
                                                $lang_code = $lang['code'];
                                                ?>

                                            @foreach($notification_message_array as $key => $item)
                                                    <?php
                                                    $temporary = []; // Reset $temporary for each $item

                                                    if(isset($item['object']->translations) && count($item['object']->translations)){
                                                        foreach($item['object']->translations as $t) {
                                                            if($t->locale == $lang['code'] && $t->key == $item['field_name'].'_message'){
                                                               // $translate[$lang_code]['message'] = $t->value;
                                                                $temporary[$lang_code]['message'] = $t->value;
                                                            }
                                                        }
                                                    }
                                                   // $translate_holder[$key] = $translate;
                                                   // $temporary = $translate;
                                                    //dump($temporary, $translate)
                                                    ?>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="d-flex align-items-center gap-3 mb-3">
                                                            <span class="text-dark">{{translate($item['field_name'].' message')}}</span>
                                                        </div>
                                                        <textarea name="{{$item['field_name']}}_message[]" class="form-control" placeholder="{{translate('Ex : Your order have been place')}}">{!! !empty($temporary) ? $temporary[$lang_code]['message'] : '' !!}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Firebase Modal -->
        <div class="modal fade" id="push-notify-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="single-item-slider owl-carousel">
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/firebase/slide-1.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Go_to_Firebase_Console')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('Open_your_web_browser_and_go_to_the_Firebase_Console')}}
                                            <a href="#" class="text--underline">
                                                {{translate('(https://console.firebase.google.com/)')}}
                                            </a>
                                        </li>
                                        <li>
                                            {{translate("Select_the_project_for_which_you_want_to_configure_FCM_from_the_Firebase_Console_dashboard.")}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/firebase/slide-2.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Navigate_to_Project_Settings')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('In_the_left-hand_menu,_click_on_the_"Settings"_gear_icon,_and_then_select_"Project_settings"_from_the_dropdown.')}}
                                        </li>
                                        <li>
                                            {{translate('In_the_Project_settings_page,_click_on_the_"Cloud_Messaging"_tab_from_the_top_menu.')}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/firebase/slide-3.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Obtain_All_The_Information_Asked!')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('In_the_Firebase_Project_settings_page,_click_on_the_"General"_tab_from_the_top_menu.')}}
                                        </li>
                                        <li>
                                            {{translate('Under_the_"Your_apps"_section,_click_on_the_"Web"_app_for_which_you_want_to_configure_FCM.')}}
                                        </li>
                                        <li>
                                            {{translate('Then_Obtain_API_Key')}}
                                        </li>
                                    </ul>
                                    <p>
                                        {{translate('Note:_Please_make_sure_to_use_the_obtained_information_securely_and_in_accordance_with_Firebase_and_FCM_documentation,_terms_of_service,_and_any_applicable_laws_and_regulations.')}}
                                    </p>

                                </div>
                            </div>

                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('/public/assets/admin/img/email-templates/3.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Write_a_message_in_the_Notification_Body')}}</h5>
                                    </div>
                                    <p>
                                        {{ translate('you_can_add_your_message_using_placeholders_to_include_dynamic_content._Here_are_some_examples_of_placeholders_you_can_use:') }}
                                    </p>
                                    <ul>
                                        <li>
                                            {userName}: {{ translate('the_name_of_the_user.') }}
                                        </li>
                                        <li>
                                            {orderId}: {{ translate('the_order_id.') }}
                                        </li>
                                        <li>
                                            {storeName}: {{ translate('store_name.') }}
                                        </li>
                                        <li>
                                            {deliveryManName}: {{ translate('deliveryman_name.') }}
                                        </li>
                                    </ul>
                                    <div class="btn-wrap">
                                        <button type="submit" class="btn btn--primary w-100" data-dismiss="modal" data-toggle="modal" data-target="#firebase-modal-2">{{translate('Got It')}}</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="slide-counter"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('[data-slide]').on('click', function(){
            let serial = $(this).data('slide')
            $(`.tab--content .item`).removeClass('show')
            $(`.tab--content .item:nth-child(${serial})`).addClass('show')
        })
    </script>

    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
                $("#from_part_2").addClass('d-none');
            }
        })
    </script>

@endpush

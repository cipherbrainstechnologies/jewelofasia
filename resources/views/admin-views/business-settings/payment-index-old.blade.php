@extends('layouts.admin.app')

@section('title', translate('Payment Setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            @include('admin-views.business-settings.partial.third-party-api-navmenu')
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{translate('payment')}} {{translate('method')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('cash_on_delivery'))
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['cash_on_delivery'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))

                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('cash_on_delivery')}}</strong>
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"  value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('inactive')}}</span>
                                    </label>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('cash_on_delivery')}}</strong>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{translate('payment')}} {{translate('method')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('digital_payment'))
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['digital_payment'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))

                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('digital')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"  value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('inactive')}}</span>
                                    </label>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('digital')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{translate('payment')}} {{translate('method')}}</h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('offline_payment'))
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['offline_payment'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))

                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('offline')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"  value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('inactive')}}</span>
                                    </label>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('offline')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['ssl_commerz_payment']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('sslcommerz')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/sslcomz.png')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="store_id"
                                           value="{{env('APP_MODE')!='demo'?$config['store_id']:''}}" placeholder="{{translate('store')}} {{translate('id')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="store_password"
                                           value="{{env('APP_MODE')!='demo'?$config['store_password']:''}}" placeholder="{{translate('store')}} {{translate('password')}}">
                                </div>
                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('sslcommerz')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/sslcomz.png')}}" alt="public">
                                </div>
                                <div class="text-right">
                                    <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['razor_pay']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('razorpay')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/razorpay.png')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <label style="padding-left: 10px"></label><br>
                                    <input type="text" class="form-control" name="razor_key"
                                           value="{{env('APP_MODE')!='demo'?$config['razor_key']:''}}" placeholder="{{translate('razorkey')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="razor_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['razor_secret']:''}}" placeholder="{{translate('razorsecret')}}">
                                </div>
                                <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('razorpay')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/razorpay.png')}}" alt="public">
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pt-4">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paypal']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paypal')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paypal.png')}}" alt="public">
                                </div>


                                <div class="form-group">
                                    <input type="text" class="form-control" name="paypal_client_id"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_client_id']:''}}" placeholder="{{translate('paypal')}} {{translate('client')}} {{translate('id')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="paypal_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['paypal_secret']:''}}" placeholder="{{translate('paypalsecret')}}">
                                </div>

                                <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paypal')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paypal.png')}}" alt="public">
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pt-4">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['stripe']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))


                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('stripe')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/stripe.png')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="published_key"
                                           value="{{env('APP_MODE')!='demo'?$config['published_key']:''}}" placeholder="{{translate('published')}} {{translate('key')}}">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']:''}}" placeholder="{{translate('api')}} {{translate('key')}}">
                                </div>
                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('stripe')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/stripe.png')}}" alt="public">
                                </div>
                                <div class="text-right">
                                    <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pt-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3"></h5>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['senang_pay']):'javascript:'}}"
                              method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('senang')}} {{translate('pay')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/senangpay.png')}}" alt="public">
                                </div>


                                <div class="form-group">
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']:''}}" placeholder="{{translate('secret')}} {{translate('key')}}">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="merchant_id"
                                           value="{{env('APP_MODE')!='demo'?$config['merchant_id']:''}}" placeholder="{{translate('merchant')}} {{translate('id')}}">
                                </div>
                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary px-5">{{translate('save')}}
                                    </button>
                                </div>
                            @else<div class="text-right">
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('senang')}} {{translate('pay')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/senangpay.png')}}" alt="public">
                                </div>
                                <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button></div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paystack']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paystack')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paystack.png')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="publicKey"
                                           value="{{env('APP_MODE')!='demo'?$config['publicKey']:''}}" placeholder="{{translate('publicKey')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="secretKey"
                                           value="{{env('APP_MODE')!='demo'?$config['secretKey']:''}}" placeholder="{{translate('secretKey')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="paymentUrl"
                                           value="{{env('APP_MODE')!='demo'?$config['paymentUrl']:''}}" placeholder="{{translate('paymentUrl')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="merchantEmail"
                                           value="{{env('APP_MODE')!='demo'?$config['merchantEmail']:''}}" placeholder="{{translate('merchantEmail')}}">
                                </div>
                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paystack')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paystack.png')}}" alt="public">
                                </div>
                            <div class="text-right">
                                    <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('bkash'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['bkash']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('bkash')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/bkash.png')}}" alt="public">
                                </div>


                                <div class="form-group">
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']??'':''}}" placeholder="{{translate('bkash')}} {{translate('api')}} {{translate('key')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="api_secret"
                                           value="{{env('APP_MODE')!='demo'?$config['api_secret']??'':''}}" placeholder="{{translate('bkash')}} {{translate('api')}} {{translate('secret')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username"
                                           value="{{env('APP_MODE')!='demo'?$config['username']??'':''}}" placeholder="{{translate('username')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="password"
                                           value="{{env('APP_MODE')!='demo'?$config['password']??'':''}}" placeholder="{{translate('password')}}">
                                </div>
                                <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button>
                                        </div>
                            @else
                            <div class="text-right">
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('bkash')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/bkash.png')}}" alt="public">
                                </div>
                                <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                                        </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['paymob']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))


                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paymob')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paymob.png')}}" alt="public">
                                </div>


                                <div class="form-group">
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='demo'?$config['api_key']??'':''}}" placeholder="{{translate('api')}} {{translate('key')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="iframe_id"
                                           value="{{env('APP_MODE')!='demo'?$config['iframe_id']??'':''}}" placeholder="{{translate('iframe_id')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="integration_id"
                                           value="{{env('APP_MODE')!='demo'?$config['integration_id']??'':''}}" placeholder="{{translate('integration_id')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="hmac"
                                           value="{{env('APP_MODE')!='demo'?$config['hmac']??'':''}}" placeholder="{{translate('hmac')}}">
                                </div>
                                <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button>
                                        </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('paymob')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/paymob.png')}}" alt="public">
                                </div>
                            <div class="text-right">
                                <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                                        </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['flutterwave']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('flutterwave')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/fluterwave.png')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']??'':''}}" placeholder="{{translate('public_key')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="secret_key"
                                           value="{{env('APP_MODE')!='demo'?$config['secret_key']??'':''}}" placeholder="{{translate('secret_key')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="hash"
                                           value="{{env('APP_MODE')!='demo'?$config['hash']??'':''}}" placeholder="{{translate('hash')}}">
                                </div>
                                <div class="text-right">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button></div>
                            @else<div class="text-right">
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('flutterwave')}}</span>
                                </h5>
                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/fluterwave.png')}}" alt="public">
                                </div>
                                <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button></div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.payment-method-update',['mercadopago']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))

                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('mercadopago')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$config?($config['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/MercadoPago_(Horizontal).svg')}}" alt="public">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="public_key"
                                           value="{{env('APP_MODE')!='demo'?$config['public_key']??'':''}}" placeholder="{{translate('public_key')}}">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="access_token"
                                           value="{{env('APP_MODE')!='demo'?$config['access_token']??'':''}}" placeholder="{{translate('access_token')}}">
                                </div>
                                <div class="text-right">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <h5 class="d-flex flex-wrap justify-content-between">
                                    <span class="text-uppercase">{{translate('mercadopago')}}</span>
                                </h5>

                                <div class="payment--gateway-img">
                                    <img src="{{asset('/public/assets/admin/img/MercadoPago_(Horizontal).svg')}}" alt="public">
                                </div>
                            <div class="text-right">
                                <button type="submit"
                                        class="btn btn-primary px-5">{{translate('configure')}}</button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script_2')
<script>

    function checkedFunc() {
        $('.switch--custom-label .toggle-switch-input').each( function() {
            if(this.checked) {
                $(this).closest('.switch--custom-label').addClass('checked')
            }else {
                $(this).closest('.switch--custom-label').removeClass('checked')
            }
        })
    }
    checkedFunc()
    $('.switch--custom-label .toggle-switch-input').on('change', checkedFunc)

</script>
@endpush

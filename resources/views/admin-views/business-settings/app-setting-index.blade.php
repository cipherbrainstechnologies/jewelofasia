@extends('layouts.admin.app')

@section('title', translate('app settings'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/app.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('system settings')}}
                </span>
            </h1>
            <ul class="nav nav-tabs border-0 mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.language.index')}}">
                        {{translate('Language Setup')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('admin.business-settings.web-app.system-setup.app_setting')}}">
                        {{translate('App Settings')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.firebase_message_config_index')}}">
                        {{translate('Firebase Configuration')}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.db-index')}}">
                        {{translate('Clean Database')}}
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Page Header -->

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{translate('Android')}}</h2>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('play_store_config'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.system-setup.app_setting',['platform' => 'android']):'javascript:'}}"
                            method="post">
                            @csrf
                            <div class="form-group mt-4">
                                <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between mb-2">
                                    <span
                                        class="pr-1 d-flex align-items-center switch--label">
                                        <span class="line--limit-1 text--title font-semibold">
                                            {{ translate('Enable download link for web footer') }}
                                        </span>
                                    </span>
                                        <input class="toggle-switch-input" type="checkbox" class="status" name="play_store_status"
                                               value="1" {{(isset($config) && $config['status']==1)?'checked':''}} hidden>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>

                                <div class="form-group">
                                    <input type="text" id="play_store_link" name="play_store_link"
                                           value="{{$config['link']??''}}" class="form-control" placeholder="">
                                </div>

                                <div class="mt-3">
                                    <label class="form-label"
                                           for="ios_min_version">
                                           <span>{{ translate('Minimum version for force update') }}</span>
                                           <span class="form-label-secondary ml-1" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate("If there is any update available in the admin panel and for that, the previous user app will not work, you can force the customer from here by providing the minimum version for force update. That means if a customer has an app below this version the customers must need to update the app first. If you don't need a force update just insert here zero (0) and ignore it.") }}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="info">
                                           </span>
                                    </label>
                                    <input type="number" min="0" step=".1" id="android_min_version" name="android_min_version"
                                           value="{{$config['min_version']??''}}" class="form-control"
                                           placeholder="{{ translate('EX: 4.0') }}">
                                </div>

                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn--primary mb-2">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{translate('IOS')}}</h2>
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('app_store_config'))
                        <form
                            action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.system-setup.app_setting',['platform' => 'ios']):'javascript:'}}"
                            method="post">
                            @csrf
                            <div class="form-group mt-4">
                                <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between mb-2">
                                    <span
                                        class="pr-1 d-flex align-items-center switch--label">
                                        <span class="line--limit-1 text--title font-semibold">
                                            {{ translate('Enable download link for web footer') }}
                                        </span>
                                    </span>
                                        <input class="toggle-switch-input" type="checkbox" class="status" name="app_store_status"
                                               value="1" {{(isset($config) && $config['status']==1)?'checked':''}} hidden>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>

                                <div class="form-group">
                                    <input type="text" id="app_store_link" name="app_store_link"
                                           value="{{$config['link']??''}}" class="form-control" placeholder="">
                                </div>

                                <div class="mt-3">
                                    <label class="form-label"
                                           for="ios_min_version">
                                           <span>{{ translate('Minimum version for force update') }}</span>
                                           <span class="form-label-secondary ml-1" data-toggle="tooltip" data-placement="right" data-original-title='{{ translate("If there is any update available in the admin panel and for that, the previous user app will not work, you can force the customer from here by providing the minimum version for force update. That means if a customer has an app below this version the customers must need to update the app first. If you don't need a force update just insert here zero (0) and ignore it.") }}'><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="info">
                                           </span>
                                    </label>
                                    <input type="number" min="0" step=".1" id="ios_min_version" name="ios_min_version"
                                           value="{{$config['min_version']??''}}" class="form-control"
                                           placeholder="{{ translate('EX: 4.0') }}">
                                </div>

                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                    class="btn btn--primary">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('script_2')

@endpush

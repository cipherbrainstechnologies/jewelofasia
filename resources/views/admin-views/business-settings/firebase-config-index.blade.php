@extends('layouts.admin.app')

@section('title', translate('firebase configuration'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/firebase.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('system settings')}}
                </span>
            </h1>
            <ul class="nav nav-tabs border-0 mb-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.language.index')}}">
                        {{ translate('Language Setup') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.app_setting')}}">
                        {{ translate('App Settings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('admin.business-settings.web-app.system-setup.firebase_message_config_index')}}">
                        {{ translate('Firebase Configuration') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.db-index')}}">
                        {{ translate('Clean Database') }}
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Page Header -->
        <div class="card">
            @php($data=\App\CentralLogics\Helpers::get_business_settings('firebase_message_config'))
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.system-setup.firebase_message_config'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($data))
                        <div class="form-group">
                            <label class="form-label">{{translate('API Key')}}</label><br>
                            <input type="text" placeholder="{{translate('Ex : AIzaSyDuBlqmsh9xw17osLOuEn7iqHtDlpkulcM')}}" class="form-control" name="apiKey"
                                   value="{{env('APP_MODE')!='demo'?$data['apiKey']:''}}" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('Auth Domain')}}</label><br>
                            <input type="text" class="form-control" name="authDomain" value="{{env('APP_MODE')!='demo'?$data['authDomain']:''}}" required autocomplete="off" placeholder="{{translate('Ex : grofresh-3986f.firebaseapp.com')}}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{translate('Project ID')}}</label><br>
                            <input type="text" class="form-control" name="projectId" value="{{env('APP_MODE')!='demo'?$data['projectId']:''}}" required autocomplete="off" placeholder="{{translate('Ex : grofresh-3986f')}}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{translate('Storage Bucket')}}</label><br>
                            <input type="text" class="form-control" name="storageBucket" value="{{env('APP_MODE')!='demo'?$data['storageBucket']:''}}" required autocomplete="off" placeholder="{{translate('Ex : grofresh-3986f.appspot.com')}}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('Messaging Sender ID')}}</label><br>
                            <input type="text" placeholder="{{translate('Ex : 250728969979')}}" class="form-control" name="messagingSenderId"
                                   value="{{env('APP_MODE')!='demo'?$data['messagingSenderId']:''}}" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('App ID')}}</label><br>
                            <input type="text" placeholder="{{translate('Ex : 1:250728969979:web:b79642a7b2d2400b75a25e')}}" class="form-control" name="appId"
                                   value="{{env('APP_MODE')!='demo'?$data['appId']:''}}" required autocomplete="off">
                        </div>

                        <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mb-2">{{translate('save')}}</button>
                        </div>
                    @else
                        <button type="submit" class="btn btn--primary">{{translate('configure')}}</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush

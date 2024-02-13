@extends('layouts.admin.app')

@section('title', translate('reCaptcha Setup'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            @include('admin-views.business-settings.partial.third-party-api-navmenu')
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <div class="flex-between">
                    <h3>{{translate('Google Recapcha Information')}}</h3>
                    <a class="cmn--btn btn--primary-2 btn-outline-primary-2" href="https://www.google.com/recaptcha/admin/create">
                        <i class="tio-info-outined"></i> {{translate('Credentials SetUp')}}
                    </a>
                </div>
                <div class="mt-4">
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('recaptcha'))
                    <form
                        action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.third-party.recaptcha_update',['recaptcha']):'javascript:'}}"
                        method="post">
                        @csrf
                        <div class="mb-4">
                           <h4>{{translate('status')}}</h4>
                        </div>
                        <div class="d-flex flex-wrap mb-4">
                            <label class="form-check form--check mr-2 mr-md-4">
                                <input type="radio" class="form-check-input" name="status"
                                    value="1" {{isset($config) && $config['status']==1?'checked':''}}>
                                <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                            </label>
                            <label class="form-check form--check">
                                <input type="radio" class="form-check-input" name="status"
                                    value="0" {{isset($config) && $config['status']==0?'checked':''}}>
                                <span class="form-check-label text--title pl-2">{{translate('inactive')}} </span>
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">{{translate('Site Key')}}</label>
                                    <input type="text" class="form-control" name="site_key"
                                            value="{{env('APP_MODE')!='demo'?$config['site_key']??"":''}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">{{translate('Secret Key')}}</label>
                                    <input type="text" class="form-control" name="secret_key"
                                            value="{{env('APP_MODE')!='demo'?$config['secret_key']??"":''}}">
                                </div>
                            </div>
                        </div>
                        <h5>{{ translate('Instructions') }}</h5>
                        <ol class="pl-3">
                            <li class="mb-1">{{ translate('To  get site key and secret keyGo to the Credentials page') }} <a
                                    href="https://www.google.com/recaptcha/admin/create" class="text--base"
                                    target="_blank">{{translate('(Click Here)')}}</a>)
                            </li>
                            <li class="mb-1">{{ translate('Add a Label (Ex: abc company)' )}}
                            </li>
                            <li class="mb-1">
                                {{ translate('Select reCAPTCHA v2  as  ReCAPTCHA Type') }}
                            </li>
                            <li class="mb-1">
                                {{ translate('Select Sub type: I m not a robot Checkbox') }}
                            </li>
                            <li class="mb-1">
                                {{ translate('Add Domain (For ex: demo.6amtech.com)') }}
                            </li>
                            <li class="mb-1">
                                {{ translate('Check in “Accept the reCAPTCHA Terms of Service”') }}
                            </li>
                            <li class="mb-1">
                                {{ translate('Press Submit') }}
                            </li>
                            <li class="mb-1">{{ translate('Copy Site Key and Secret Key, Paste in the input filed below and Save.') }}
                            </li>
                        </ol>

                        <div class="text-right">
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                            onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                            class="btn btn--primary px-5">{{translate('save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush

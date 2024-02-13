@extends('layouts.admin.app')

@section('title', translate('OTP Setup'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    @include('admin-views.business-settings.partial.business-settings-navmenu')

    @php($config=\App\CentralLogics\Helpers::get_business_settings('maintenance_mode'))
    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.otp-setup-update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @php($maximum_otp_hit=\App\Model\BusinessSetting::where('key','maximum_otp_hit')->first()->value)
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="maximum_otp_hit">{{translate('maximum OTP submit attempt')}}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('The maximum OTP hit is a measure of how many times a specific one-time password has been generated and used within a time.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$maximum_otp_hit}}"
                                           name="maximum_otp_hit" class="form-control" placeholder="" required>
                                </div>
                            </div>
                            @php($otp_resend_time=\App\Model\BusinessSetting::where('key','otp_resend_time')->first()->value)
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="otp_resend_time">{{translate('OTP resend time')}}
                                        <span class="text-danger">( {{ translate('in second') }} )</span>
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the user fails to get the OTP within a certain time, user can request a resend.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$otp_resend_time}}"
                                           name="otp_resend_time" class="form-control" placeholder="" required>
                                </div>
                            </div>
                            @php($temporary_block_time=\App\Model\BusinessSetting::where('key','temporary_block_time')->first()->value)
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="temporary_block_time">{{translate('temporary_OTP_block_time')}}
                                        <span class="text-danger">( {{ translate('in second') }} )</span>
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('Temporary OTP block time refers to a security measure implemented by systems to restrict access to OTP service for a specified period of time for wrong OTP submission.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$temporary_block_time}}"
                                           name="temporary_block_time" class="form-control" placeholder="" required>
                                </div>
                            </div>

                            @php($maximum_login_hit=\App\Model\BusinessSetting::where('key','maximum_login_hit')->first()->value)
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="maximum_otp_hit">{{translate('maximum Login Attempt')}}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('The maximum login hit is a measure of how many times a user can submit password within a time.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$maximum_login_hit}}"
                                           name="maximum_login_hit" class="form-control" placeholder="" required>
                                </div>
                            </div>
                            @php($temporary_login_block_time=\App\Model\BusinessSetting::where('key','temporary_login_block_time')->first()->value)
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="temporary_block_time">{{translate('temporary_login_block_time')}}
                                        <span class="text-danger">( {{ translate('in second') }} )</span>
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('Temporary login block time refers to a security measure implemented by systems to restrict access for a specified period of time for wrong Password submission.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$temporary_login_block_time}}"
                                           name="temporary_login_block_time" class="form-control" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
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

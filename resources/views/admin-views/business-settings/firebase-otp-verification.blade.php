@extends('layouts.admin.app')

@section('title', translate('Firebase OTP Verification'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            @include('admin-views.business-settings.partial.third-party-api-navmenu')
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">

                <div class="card">

                    <div class="card-body">
                        <form action="{{route('admin.business-settings.web-app.third-party.firebase-otp-verification-update')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6" style="padding-top: 30px;">
                                    <?php
                                    $firebase_otp=\App\CentralLogics\Helpers::get_business_settings('firebase_otp_verification');
                                    ?>
                                    <div class="form-group">
                                        <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                            <span class="pr-1 d-flex align-items-center switch--label">
                                                <span class="line--limit-1">
                                                    <strong>{{translate('Firebase OTP Verification Status')}}</strong>
                                                </span>
                                            </span>
                                            <input type="checkbox" class="toggle-switch-input" name="status" {{ isset($firebase_otp) && $firebase_otp['status'] == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize">{{translate('web_api_key')}}</label>
                                        <input type="text" value="{{$firebase_otp && env('APP_MODE')!='demo' ? $firebase_otp['web_api_key'] : ''}}" name="web_api_key" class="form-control" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                        class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>
@endpush

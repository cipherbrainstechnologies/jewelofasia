@extends('layouts.admin.app')

@section('title', translate('mail config'))

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
            <div class="col-xl-8">
                <div class="card mb-3">
                    <div class="card-body">

                        <div class="position-relative">
                                <button class="btn btn-secondary" type="button" data-toggle="collapse"
                                        data-target="#collapseExample" aria-expanded="false"
                                        aria-controls="collapseExample">
                                    <i class="tio-email-outlined"></i>
                                    {{translate('test_your_email_integration')}}
                                </button>
                            <div class="fixed--to-right">
                                <i class="tio-telegram float-right"></i>
                            </div>
                        </div>

                        <div class="collapse" id="collapseExample">
                            <form class="pt-3" action="javascript:">
                                <div class="row g-2">
                                    <div class="col-sm-8">
                                        <div class="form-group mb-0">
                                            <label for="inputPassword2"
                                                    class="sr-only">{{translate('mail')}}</label>
                                            <input type="email" id="test-email" class="form-control"
                                                    placeholder="Ex : jhon@email.com">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="button" onclick="send_mail()" class="btn btn-primary h-100 btn-block">
                                            <i class="tio-telegram"></i>
                                            {{translate('send_mail')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            @php($config=\App\Model\BusinessSetting::where(['key'=>'mail_config'])->first())
            @php($data=json_decode($config['value'],true))
            @php($status=$data['status']== 1 ? 0 : 1)
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <label class="control-label h3 mb-0 text-capitalize mr-3">{{translate('mail configuration status')}}</label>
                            <div class="custom--switch">
                                <input type="checkbox" name="status" value="" id="switch6" switch="primary"
                                    onclick="mail_status_change('{{route('admin.business-settings.web-app.mail-config.status',[$status])}}')"
                                    class="toggle-switch-input" id="stocksCheckbox{{ 1 }}" {{ $data['status'] ==  1 ? 'checked' : '' }}>
                                <label for="switch6" data-on-label="on" data-off-label="off"></label>
                            </div>
                        </div>
                        <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.mail-config'):'javascript:'}}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @if(isset($config))
                                <div class="row mt-3">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('mailer')}} {{translate('name')}}</label><br>
                                        <input type="text" placeholder="{{ translate('ex : Alex') }}" class="form-control" name="name"
                                            value="{{env('APP_MODE')!='demo'?$data['name']:''}}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('host')}}</label><br>
                                        <input type="text" class="form-control" name="host" value="{{env('APP_MODE')!='demo'?$data['host']:''}}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('driver')}}</label><br>
                                        <input type="text" class="form-control" name="driver" value="{{env('APP_MODE')!='demo'?$data['driver']:''}}" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('port')}}</label><br>
                                        <input type="text" class="form-control" name="port" value="{{env('APP_MODE')!='demo'?$data['port']:''}}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('username')}}</label><br>
                                        <input type="text" placeholder="{{ translate('ex : ex@yahoo.com') }}" class="form-control" name="username"
                                            value="{{env('APP_MODE')!='demo'?$data['username']:''}}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('email')}} {{translate('id')}}</label><br>
                                        <input type="text" placeholder="{{ translate('ex : ex@yahoo.com') }}" class="form-control" name="email"
                                            value="{{env('APP_MODE')!='demo'?$data['email_id']:''}}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('encryption')}}</label><br>
                                        <input type="text" placeholder="{{ translate('ex : tls') }}" class="form-control" name="encryption"
                                            value="{{env('APP_MODE')!='demo'?$data['encryption']:''}}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="form-label">{{translate('password')}}</label><br>
                                        <input type="text" class="form-control" name="password" value="{{env('APP_MODE')!='demo'?$data['password']:''}}" required>
                                    </div>
                                </div>
                                <div class="btn--container justify-content-end">
                                    <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary mt-2 mb-2">{{translate('save')}}</button>
                                </div>
                            @else
                                <button type="submit" class="btn btn-primary mt-2 mb-2">{{translate('configure')}}</button>
                            @endif
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>

    function mail_status_change(route) {

        $.get({
            url: route,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                toastr.success(data.message);
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    }

    function ValidateEmail(inputText) {
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (inputText.match(mailformat)) {
            return true;
        } else {
            return false;
        }
    }

    function send_mail() {
        if (ValidateEmail($('#test-email').val())) {
            Swal.fire({
                title: '{{translate('Are you sure?')}}?',
                text: "{{translate('a_test_mail_will_be_sent_to_your_email')}}!",
                showCancelButton: true,
                confirmButtonColor: '#F56A57',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{translate('Yes')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.business-settings.web-app.mail-send')}}",
                        method: 'POST',
                        data: {
                            "email": $('#test-email').val()
                        },
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            if (data.success === 2) {
                                toastr.error('{{translate('email_configuration_error')}} !!');
                            } else if (data.success === 1) {
                                toastr.success('{{translate('email_configured_perfectly!')}}!');
                            } else {
                                toastr.info('{{translate('email_status_is_not_active')}}!');
                            }
                        },
                        complete: function () {
                            $('#loading').hide();

                        }
                    });
                }
            })
        } else {
            toastr.error('{{translate('invalid_email_address')}} !!');
        }
    }
</script>
@endpush

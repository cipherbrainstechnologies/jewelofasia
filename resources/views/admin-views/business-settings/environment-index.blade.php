@extends('layouts.admin.app')

@section('title', translate('Settings'))

@push('css_or_js')
    <script src="https://use.fontawesome.com/74721296a6.js"></script>
    <link rel="stylesheet"
          href=
          "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .input-icons i {
            position: absolute;
            cursor: pointer;
            /*text-align: right*/
            background-color: #F8FAFD;
            border: #E7EAF3 1px solid;
        }

        .input-icons {
            width: 100%;
            margin-bottom: 10px;
        }

        .icon {
            padding: 10px;
            min-width: 40px;
        }

        .input-field {
            width: 94%;
            padding: 10px;
            text-align: center;
            border-right-style: none;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('Environment Setup')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="alert alert-danger mx-2" role="alert">
                    {{translate('This_page_is_having_sensitive_data.Make_sure_before_changing.')}}
                </div>
                <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                    <form action="{{route('admin.business-settings.update-environment')}}" method="post"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('APP_NAME')}}</label>
                                    <input type="text" value="{{ env('APP_NAME') }}"
                                           name="app_name" class="form-control"
                                           placeholder="{{translate('Ex : EFood')}}" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('APP_DEBUG')}}</label>
                                    <select name="app_debug" class="form-control js-select2-custom">
                                        <option value="true" {{env('APP_DEBUG')==1?'selected':''}}>
                                            {{translate('True')}}
                                        </option>
                                        <option value="false" {{env('APP_DEBUG')==0?'selected':''}}>
                                            {{translate('False')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('APP_MODE')}}</label>
                                    <select name="app_mode" class="form-control js-select2-custom">
                                        <option value="live" {{env('APP_MODE')=='live'?'selected':''}}>
                                            {{translate('Live')}}
                                        </option>
                                        <option value="dev" {{env('APP_MODE')=='dev'?'selected':''}}>
                                            {{translate('Dev')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('APP_URL')}}</label>
                                    <input type="text" value="{{ env('APP_URL') }}"
                                           name="app_url" class="form-control"
                                           placeholder="{{translate('Ex : http://localhost')}}" required disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_CONNECTION')}}</label>
                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('DB_CONNECTION') : '---' }}"
                                           name="db_connection" class="form-control"
                                           placeholder="{{translate('Ex : mysql')}}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_HOST')}}</label>
                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('DB_HOST') : '---' }}"
                                           name="db_host" class="form-control"
                                           placeholder="{{translate('Ex : http://localhost/')}}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_PORT')}}</label>
                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('DB_PORT') : '---' }}"
                                           name="db_port" class="form-control"
                                           placeholder="{{translate('Ex : 3306')}}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_DATABASE')}}</label>
                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('DB_DATABASE') : '---' }}"
                                           name="db_database" class="form-control"
                                           placeholder="{{translate('Ex : demo_db')}}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_USERNAME')}}</label>
                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('DB_USERNAME') : '---' }}"
                                           name="db_username" class="form-control"
                                           placeholder="{{translate('Ex : root')}}" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group" id="db_password_div">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('DB_PASSWORD')}}</label>
                                    <div class="input-icons">
                                        <input type="password" value="{{ env('APP_MODE') != 'demo' ? env('DB_PASSWORD') : '---' }}" class="form-control input-field" id="db_password" style="display: inline" disabled>
                                        <i class="fa fa-eye icon align-middle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('BUYER_USERNAME')}}</label>

                                    <input type="text" value="{{ env('APP_MODE') != 'demo' ? env('BUYER_USERNAME') : '---' }}" class="form-control"
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group" id="purchase_code_div">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('PURCHASE_CODE')}}</label>
                                    <div class="input-icons">
                                        <input type="password" value="{{ env('APP_MODE') != 'demo' ? env('PURCHASE_CODE') : '---' }}" class="form-control input-field" id="purchase_code" style="display: inline" disabled>
                                        <i class="fa fa-eye icon align-middle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                class="btn btn-primary mb-2">{{translate('submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).ready(function () {
            $("#purchase_code_div").click(function () {
                var type = $('#purchase_code').get(0).type;
                if (type === 'password') {
                    $('#purchase_code').get(0).type = 'text';
                } else if (type === 'text') {
                    $('#purchase_code').get(0).type = 'password';
                }
            });

            $("#db_password_div").click(function () {
                var type = $('#db_password').get(0).type;
                if (type === 'password') {
                    $('#db_password').get(0).type = 'text';
                } else if (type === 'text') {
                    $('#db_password').get(0).type = 'password';
                }
            });
        })
    </script>

    <script>
        $("form").on('submit',function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{translate('Are you sure?')}}',
                text: "{{translate('Sensitive_data! Make_sure_before_changing.')}}",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.submit();
                }else{
                    e.preventDefault();
                    toastr.success("{{translate('Cancelled')}}");
                    location.reload();
                }
            })
        });
    </script>
@endpush

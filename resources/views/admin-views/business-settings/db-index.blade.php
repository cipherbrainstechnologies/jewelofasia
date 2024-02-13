@extends('layouts.admin.app')

@section('title', translate('clean database'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/cloud-database.png')}}" class="w--26" alt="">
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
                    <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.firebase_message_config_index')}}">
                        {{ translate('Firebase Configuration') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{route('admin.business-settings.web-app.system-setup.db-index')}}">
                        {{ translate('Clean Database') }}
                    </a>
                </li>
            </ul>
        </div>
        <!-- End Page Header -->
        <div class="alert alert--danger alert-danger mb-3" role="alert">
            <span class="alert--icon"><i class="tio-info"></i></span>
                <strong class="text--title">{{translate('Note :')}}</strong>
            <span>
                {{translate('This_page_contains_sensitive_information.Make_sure_before_changing.')}}
            </span>
        </div>

        <div class="card">
            <div class="card-body p-20">
                <form action="{{route('admin.business-settings.web-app.system-setup.clean-db')}}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="check--item-wrapper clean--database-checkgroup mt-0">
                        @foreach($tables as $key=>$table)
                            <div class="check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="tables[]" value="{{$table}}"
                                        class="form-check-input"
                                        id="{{$table}}">
                                    <label class="form-check-label text-dark"
                                        for="{{$table}}">{{ translate(Str::limit($table, 20)) }}
                                    <span class="badge-pill badge-secondary mx-2">{{$rows[$key]}}</span></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                            onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                            class="btn btn-primary mb-2">{{translate('clean')}}</button>
                    </div>
                </form>
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
                cancelButtonText: '{{translate('No')}}',
                confirmButtonText: '{{translate('Yes')}}',
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

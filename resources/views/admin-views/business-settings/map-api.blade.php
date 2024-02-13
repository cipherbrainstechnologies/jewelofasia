@extends('layouts.admin.app')

@section('title', translate('Map API Settings'))

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
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.web-app.third-party.map-api-store'):'javascript:'}}" method="post">
                    @csrf
                    <div class="row">
                        @php($key=\App\Model\BusinessSetting::where('key','map_api_server_key')->first()?->value )
                        <div class="form-group col-md-6">
                            <label class="form-label">{{translate('map_api_server')}} {{translate('key')}}</label>
                            <textarea name="map_api_server_key" class="form-control">{{env('APP_MODE')!='demo'?$key:''}}</textarea>
                        </div>
                        @php($key=\App\Model\BusinessSetting::where('key','map_api_client_key')->first()?->value)
                        <div class="form-group col-md-6">
                            <label class="form-label">{{translate('map_api_client')}} {{translate('key')}}</label>
                            <textarea name="map_api_client_key" class="form-control">{{env('APP_MODE')!='demo'?$key:''}}</textarea>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button class="btn btn--reset" type="reset">{{translate('reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn-primary">{{translate('save')}}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection



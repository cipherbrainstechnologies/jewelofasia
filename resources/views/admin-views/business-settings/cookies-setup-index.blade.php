@extends('layouts.admin.app')

@section('title', translate('Cookies Setup'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    @include('admin-views.business-settings.partial.business-settings-navmenu')

    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <form action="{{route('admin.business-settings.store.cookies-setup-update')}}" method="post" enctype="multipart/form-data">
                @csrf
                @php($cookies=\App\CentralLogics\Helpers::get_business_settings('cookies'))
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <span class="">{{translate('Cookies Text')}}</span>
                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{$cookies?($cookies['status']==1? translate('off'): translate('on')):''}}</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{$cookies?($cookies['status']==0? translate('on'): translate('off')):''}}</span>
                                        <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{$cookies?($cookies['status']==1?'checked':''):''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                                <div class="form-group pt-3">
                                    <textarea name="text" class="form-control" rows="6" placeholder="{{ translate('Cookies text') }}" required>{{$cookies['text']}}</textarea>
                                </div>
                                <div class="btn--container justify-content-end">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                            class="btn btn--primary">{{translate('save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection

@push('script_2')

@endpush

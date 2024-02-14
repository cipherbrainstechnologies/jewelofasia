@extends('layouts.admin.app')

@section('title', translate('Update zipcode'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/category.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{translate('zipcode_setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row g-2">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body pt-sm-0 pb-sm-4">
                        <form action="{{route('admin.zipcodes.update',[$zipcodes['id']])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @php($data = Helpers::get_business_settings('language'))
                            @php($default_lang = Helpers::get_default_language())
                            {{-- @php($default_lang = 'en') --}}
                            @if ($data && array_key_exists('code', $data[0]))
                                {{-- @php($default_lang = json_decode($language)[0]) --}}
                                <ul class="nav nav-tabs d-inline-flex mb-5">
                                    @foreach ($data as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}" href="#"
                                        id="{{ $lang['code'] }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="row  g-4">
                                    @foreach ($data as $lang)
                                        <div class="col-sm-6">
                                            <label for="city">{{ translate('City') }}</label>
                                            <select name="city" class="form-control" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @foreach($city as $ct)
                                                    <option value="{{$ct['id']}}" @if($ct['id'] == $zipcodes['city_id']) selected @endif>{{ $ct['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6 {{ $lang['default'] == false ? 'd-none' : '' }} lang_form" id="{{ $lang['code'] }}-form">
                                            <div class="col-lg-12">
                                                <label class="form-label" for="exampleFormControlInput1">{{ translate('zipcode') }} ({{ strtoupper($lang['code']) }})</label>
                                                <input type="text" name="zipcode" class="form-control" placeholder="{{ translate('zipcode') }}" required
                                                    oninvalid="this.setCustomValidity('Zipcode is required')"
                                                    oninput="this.setCustomValidity('')"
                                                    value="{{ isset($zipcodes) ? $zipcodes->zipcode : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="order_before_day">{{ translate('Order Before Day') }}</label>
                                            <select name="order_before_day" class="form-control" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @if(!empty($days))
                                                    @foreach($days as $day)
                                                        <option value="{{$day}}" @if($day==$zipcodes['order_before_day']) selected @endif>{{ucfirst($day)}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="delivery_order_day">{{ translate('Delivery Days') }}</label>
                                            <select name="delivery_order_day[]" class="form-control js-select2-custom" multiple="multiple" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @if(!empty($days))
                                                    @foreach($days as $day)
                                                        <option value="{{$day}}" @if(in_array($day, explode(',',$zipcodes['delivery_order_day']))) selected @endif>{{ucfirst($day)}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="col-sm-6">
                                            <label for="status">{{ translate('Status') }}</label>
                                            <select name="status" class="form-control" required>
                                                <option value="active" @if($zipcodes['status']=='active') selected @endif>{{ translate('Active') }}</option>
                                                <option value="inactive" @if($zipcodes['status']=='inactive') selected @endif>{{ translate('Inactive') }}</option>
                                            </select>
                                        </div>
                                        
                                        <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                    @endforeach
                            @else
                            <div class="col-sm-6">
                                            <label for="city">{{ translate('City') }}</label>
                                            <select name="city" class="form-control" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @foreach($city as $ct)
                                                    <option value="$ct['id']">{{ $ct['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6 {{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                                id="{{ $lang['code'] }}-form">
                                            <div class="col-lg-12">
                                                 <label class="form-label"
                                                    for="exampleFormControlInput1">{{translate('zipcode')}}
                                                ({{ strtoupper($lang['code']) }})</label>
                                                <input type="text" name="zipcode" class="form-control" placeholder="{{translate('zipcode')}} " data-role="tagsinput"
                                                    {{$lang['status'] == true ? 'required':''}}
                                                    @if($lang['status'] == true) oninvalid="document.getElementById('{{$lang['code']}}-link').click()" @endif>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label for="order_before_day">{{ translate('Order Before Day') }}</label>
                                            <select name="order_before_day" class="form-control" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @if(!empty($days))
                                                    @foreach($days as $day)
                                                        <option value="{{$day}}" @if($day=="sunday") selected @endif>{{ucfirst($day)}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="delivery_order_day">{{ translate('Delivery Days') }}</label>
                                            <select name="delivery_order_day[]" class="form-control js-select2-custom" multiple="multiple" required>
                                                <option value="">{{ translate('Select') }}</option>
                                                @if(!empty($days))
                                                    @foreach($days as $day)
                                                        <option value="{{$day}}">{{ucfirst($day)}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>


                                        <div class="col-sm-6">
                                            <label for="status">{{ translate('Status') }}</label>
                                            <select name="status" class="form-control" required>
                                                <option value="active">{{ translate('Active') }}</option>
                                                <option value="inactive">{{ translate('Inactive') }}</option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                                    @endif
                                    
                                    <div class="col-12">
                                        <div class="btn--container justify-content-end">
                                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script src="{{asset('public/assets/admin/js/tags-input.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("input[data-role=tagsinput]").tagsinput({
            confirmKeys: [13, 44, 32], // Enter, comma, and space trigger tag creation
        });
    });
</script>
@endpush

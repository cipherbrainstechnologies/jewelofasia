@extends('layouts.admin.app')

@section('title', translate('Add new zipcode'))

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
                        <form action="{{route('admin.zipcodes.store')}}" method="post" enctype="multipart/form-data">
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
                                                    <option value="{{$ct['id']}}">{{ $ct['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6 {{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                                id="{{ $lang['code'] }}-form">
                                            <div class="col-lg-12 ">
                                                 <label class="form-label"
                                                    for="exampleFormControlInput1">{{translate('zipcode')}}
                                                ({{ strtoupper($lang['code']) }})</label>
                                                <input type="text" name="zipcode[]" class="form-control" placeholder="{{translate('zipcode')}} " data-role="tagsinput" oninvalid="this.setCustomValidity('Zipcode is required')" oninput="this.setCustomValidity('')">
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
                                                <input type="text" name="zipcode" class="form-control" placeholder="{{translate('zipcode')}} " data-role="tagsinput">
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

            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card--header">
                            <h5 class="card-title">{{translate('Zipcode Table')}} <span class="badge badge-soft-secondary">{{ $zipcodes->total() }}</span> </h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" maxlength="255"
                                           class="form-control pl-5"
                                           placeholder="{{translate('Search_by_Name')}}" aria-label="Search"
                                           value="{{$search}}" required autocomplete="off">
                                           <i class="tio-search tio-input-search"></i>
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{translate('search')}}
                                        </button>
                                    </div>
                                    <div class="col-sm-6 col-md-12 col-lg-4 __btn-row">
                                        <a href="{{route('admin.category.add')}}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center">{{translate('#')}}</th>
                                <th>{{translate('zipcode')}}</th>
                                <th>{{translate('city')}}</th>
                                <th>{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($zipcodes as $key=>$zipcode)
                                <tr>
                                    <td class="text-center">{{$zipcodes->firstItem()+$key}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body text-trim-50 text-wrap-normal">
                                            {{$zipcode['zipcode']}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-block font-size-sm text-body text-trim-50">
                                            {{$zipcode['city']['name']}}
                                        </span>
                                    </td>
                                    <td>

                                        <label class="toggle-switch">
                                            <input type="checkbox"
                                                onclick="status_change_alert('{{ route('admin.zipcodes.status', [$zipcode->id, $zipcode->status ? 0 : 1]) }}', '{{ $ct->status? translate('you_want_to_disable_this_city'): translate('you_want_to_active_this_city') }}', event)"
                                                class="toggle-switch-input" id="stocksCheckbox{{ $ct->id }}"
                                                {{ $zipcode->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>

                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.zipcodes.edit',[$zipcode['id']])}}">
                                            <i class="tio-edit"></i></a>
                                            <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('city-{{$zipcode['id']}}','{{ translate("Want to delete this") }}')">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.zipcodes.delete',[$zipcode['id']])}}"
                                                method="post" id="city-{{$zipcode['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                                
                        @if(count($zipcodes) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                            <p class="mb-0">{{translate('No_data_to_show')}}</p>
                        </div>
                        @endif

                        <table>
                            <tfoot>
                            {!! $zipcodes->links() !!}
                            </tfoot>
                        </table>

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

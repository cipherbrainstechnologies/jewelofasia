@extends('layouts.admin.app')

@section('title', translate('Product Setup'))


@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    @include('admin-views.business-settings.partial.business-settings-navmenu')

    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.product-setup-update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @php($stock_limit=\App\Model\BusinessSetting::where('key','minimum_stock_limit')->first()->value)
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="minimum_stock_limit">{{translate('minimum stock limit')}}</label>
                                    <input type="number" min="1" value="{{$stock_limit}}"
                                           name="minimum_stock_limit" class="form-control" placeholder="" required>
                                </div>
                            </div>
                            @php($tax_status= \App\CentralLogics\Helpers::get_business_settings('product_vat_tax_status'))
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Product VAT/TAX Status (Included/Excluded)')}}</label>
                                    <select name="product_vat_tax_status" class="form-control">
                                        <option value="excluded" {{$tax_status =='excluded'?'selected':''}}>{{translate('excluded')}}</option>
                                        <option value="included" {{$tax_status =='included'?'selected':''}}>{{translate('included')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 mb-4">
                                @php($featured_product_status=\App\CentralLogics\Helpers::get_business_settings('featured_product_status'))
                                <div class="d-flex flex-wrap justify-content-between">
                                    <label class="input-label">{{translate('featured')}} {{ translate('product') }}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the status is off feature product will not show to user.') }}">
                                        </i>
                                    </label>

                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{$featured_product_status==1? translate('off'): translate('on')}}</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{$featured_product_status==1? translate('off'): translate('on')}}</span>
                                        <input type="checkbox" name="featured_product_status" value="1" class="toggle-switch-input" {{$featured_product_status==1?'checked':''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 mb-4">
                                @php($trending_product_status=\App\CentralLogics\Helpers::get_business_settings('trending_product_status'))
                                <div class="d-flex flex-wrap justify-content-between">
                                    <label class="input-label">{{translate('trending')}} {{ translate('product') }}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the status is off trending product will not show to user.') }}">
                                        </i>
                                    </label>

                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{$trending_product_status==1? translate('off'): translate('on')}}</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{$trending_product_status==0? translate('on'): translate('off')}}</span>
                                        <input type="checkbox" name="trending_product_status" value="1" class="toggle-switch-input" {{$trending_product_status==1?'checked':''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 mb-4">
                                @php($most_reviewed_product_status=\App\CentralLogics\Helpers::get_business_settings('most_reviewed_product_status'))
                                <div class="d-flex flex-wrap justify-content-between">
                                    <label class="input-label">{{translate('most')}} {{ translate('reviewed') }} {{ translate('product') }}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the status is off most reviewed product will not show to user.') }}">
                                        </i>
                                    </label>

                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{$most_reviewed_product_status==1? translate('off'): translate('on')}}</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{$most_reviewed_product_status==0? translate('on'): translate('off')}}</span>
                                        <input type="checkbox" name="most_reviewed_product_status" value="1" class="toggle-switch-input" {{$most_reviewed_product_status==1?'checked':''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 mb-4">
                                @php($recommended_product_status=\App\CentralLogics\Helpers::get_business_settings('recommended_product_status'))
                                <div class="d-flex flex-wrap justify-content-between">
                                    <label class="input-label">{{translate('recommended')}} {{ translate('product') }}
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the status is off recommended product will not show to user.') }}">
                                        </i>
                                    </label>

                                    <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                        <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{$recommended_product_status==1? translate('off'): translate('on')}}</span>
                                        <span class="mr-2 switch--custom-label-text off text-uppercase">{{$recommended_product_status==0? translate('on'): translate('off')}}</span>
                                        <input type="checkbox" name="recommended_product_status" value="1" class="toggle-switch-input" {{$recommended_product_status==1?'checked':''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
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

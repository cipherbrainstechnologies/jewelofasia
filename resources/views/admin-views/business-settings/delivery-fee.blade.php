@extends('layouts.admin.app')

@section('title', translate('delivery fee setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">

            @include('admin-views.business-settings.partial.business-settings-navmenu')
        </div>
        @php($config=\App\CentralLogics\Helpers::get_business_settings('maintenance_mode'))
        <div class="tab-content">
            <div class="tab-pane active" id="delivery-fee">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span> <span>{{translate('Delivery Fee Setup')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.store.delivery-setup-update')}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @php($config=\App\CentralLogics\Helpers::get_business_settings('delivery_management'))
                                <div class="col-lg-6 text-capitalize">
                                    <label class="form-label font-semibold">{{translate('Delivery Type')}}</label>

                                    <div class="d-flex flex-wrap align-items-center form-control border">
                                        <label class="form-check form--check mr-2 mr-md-4 mb-0">
                                            <input type="radio" name="shipping_status" value="0" {{$config['status']==0?'checked':''}} id="default_delivery_status" class="form-check-input">
                                            <span class="form-check-label">
                                                    {{translate('default_delivery_charge')}}
                                            </span>
                                        </label>
                                        <label class="form-check form--check mr-2 mr-md-4 mb-0">
                                            <input type="radio" name="shipping_status" value="1" {{$config['status']==1?'checked':''}} id="shipping_by_distance_status" class="form-check-input">
                                            <span class="form-check-label">
                                                    {{translate('delivery_charge_by_distance')}}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 pt-5">
                                    <?php
                                    $free_delivery_status=\App\CentralLogics\Helpers::get_business_settings('free_delivery_over_amount_status');
                                    $d_status = $free_delivery_status == 1 ? 0 : 1;
                                    ?>
                                    <div class="form-group">
                                        <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control"
                                               onclick="free_delivery_over_amount_status('{{route('admin.business-settings.store.free-delivery-status',[$d_status])}}')">
                                            <span class="pr-1 d-flex align-items-center switch--label">
                                                <span class="line--limit-1">
                                                    <strong>{{translate('free_delivery_over_amount_status')}}</strong>
                                                </span>
                                                <span class="form-label-secondary text-danger d-flex ml-1" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this field is active and the order amount exceeds this free delivery over amount then the delivery fee will be free.')}}"><img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="info">
                                                </span>
                                            </span>
                                            <input type="checkbox" class="toggle-switch-input" name="free_delivery_status" {{ $free_delivery_status == 1 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div>
                                                <label>{{translate('minimum_shipping_charge')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label><br>
                                                <input type="number" step=".01" class="form-control"
                                                       name="min_shipping_charge"
                                                       value="{{$config['min_shipping_charge']}}"
                                                       id="min_shipping_charge" {{ $config['status']==0?'disabled':'' }} >
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div>
                                                <label>{{translate('shipping_charge_per_km')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label><br>
                                                <input type="number" step=".01" class="form-control" name="shipping_per_km"
                                                       value="{{$config['shipping_per_km']}}"
                                                       id="shipping_per_km" {{ $config['status']==0?'disabled':'' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 mt-4">
                                    @php($delivery=\App\Model\BusinessSetting::where('key','delivery_charge')->first()->value)
                                    <label class="" for="exampleFormControlInput1">{{translate('default_delivery_charge')}} <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span></label>
                                    <input type="number" min="0" step=".01" name="delivery_charge" value="{{$delivery}}" class="form-control" placeholder="EX: 100" required
                                           {{ $config['status']==1?'disabled':'' }} id="delivery_charge">
                                </div>

                                @php($free_delivery_over_amount=\App\CentralLogics\Helpers::get_business_settings('free_delivery_over_amount'))
                                <div class="col-md-6 col-sm-6 mt-4">
                                    <div class="form-group mb-0">
                                        <label class="">
                                            {{translate('free_delivery_over_amount')}}
                                            <span>({{ \App\CentralLogics\Helpers::currency_symbol() }})</span>
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('If the order amount exceeds this amount the delivery fee will be free.') }}"></i>

                                        </label>
                                        <input type="number" value="{{$free_delivery_over_amount}}" name="free_delivery_over_amount" class="form-control" placeholder=""
                                               {{ $free_delivery_status == 0 ? 'readonly' : '' }} required>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container mt-4 justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}" class="btn btn--primary">{{translate('submit')}}</button>
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
        $('#shipping_by_distance_status').on('click', function () {
            $("#delivery_charge").prop('disabled', true);
            $('#min_shipping_charge').prop('disabled', false);
            $('#shipping_per_km').prop('disabled', false);
        });

        $('#default_delivery_status').on('click', function () {
            $("#delivery_charge").prop('disabled', false);
            $('#min_shipping_charge').prop('disabled', true);
            $('#shipping_per_km').prop('disabled', true);
        });

        function free_delivery_over_amount_status(route) {

            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                    if (data.status == 1) {
                        toastr.success(data.message);
                    } else {
                        toastr.warning(data.message);
                    }

                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }
    </script>

@endpush

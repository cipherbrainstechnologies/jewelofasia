@extends('layouts.admin.app')

@section('title', translate('Sale Report'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="media align-items-center">
                <img class="w--20" src="{{asset('public/assets/admin')}}/svg/illustrations/credit-card.svg"
                     alt="Image Description">
                <div class="media-body pl-3">
                    <h1 class="page-header-title mb-1">{{translate('sale')}} {{translate('report')}} {{translate('overview')}}</h1>
                    <div>
                        <span>{{translate('admin')}}:</span>
                        <a href="#" class="text--primary-2">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div>
            <div class="card">
                <!-- Header -->
                <div class="card-header border-0">
                    <div class="w-100 pt-3">

                        <form class="w-100">
                            <div class="row g-3 g-sm-4 g-md-3 g-lg-4">
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    <select class="custom-select custom-select-sm text-capitalize min-h-45px" name="branch_id">
                                        <option disabled selected>--- {{translate('select')}} {{translate('branch')}} ---</option>
                                        <option value="all" {{ $branch_id == 'all' ? 'selected': ''}}>{{translate('all')}} {{translate('branch')}}</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch['id']}}" {{ $branch['id'] == $branch_id ? 'selected' : ''}}>{{$branch['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-date-group">
                                        <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                        <label class="input-date">
                                            <input type="text" id="start_date" name="start_date" value="{{$start_date}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-date-group">
                                        <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                        <label class="input-date">
                                            <input type="text" id="end_date" name="end_date" value="{{$end_date}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-12 col-lg-4 __btn-row">
                                    <a href="{{route('admin.report.sale-report')}}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                                    <button type="submit" id="show_filter_data" class="btn w-100 btn--primary min-h-45px">{{translate('show data')}}</button>
                                </div>
                            </div>
                        </form>

                        <div class="col-md-12 pt-4">
                            <div class="report--data">
                                <div class="row g-3">
                                    <div class="col-sm-4">
                                        <div class="order--card h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                    <span>{{translate('total orders')}}</span>
                                                </h6>
                                                <span class="card-title text-success" id="order_count">{{ count($orders) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="order--card h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                    <span>{{translate('total item qty')}}</span>
                                                </h6>
                                                <span class="card-title text-success" id="item_count">{{ $total_qty }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="order--card h-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                    <span>{{translate('total amount')}}</span>
                                                </h6>
                                                <span class="card-title text-success" id="order_amount">{{ $total_sold }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive" id="set-rows">
                    <table class="table table-borderless table-align-middle">
                        <thead class="thead-light">
                        <tr>
                            <th>{{translate('#')}} </th>
                            <th>{{translate('product info')}}</th>
                            <th>{{translate('qty')}}</th>
                            <th>{{translate('date')}}</th>
                            <th>{{translate('amount')}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($order_details as $key=>$detail)
                           <?php
                                $price = $detail['price'] - $detail['discount_on_product'];
                                $ord_total = $price * $detail['quantity'];

                                $product = json_decode($detail->product_details, true);
                                $images = $product['image'] != null ? (gettype($product['image'])!='array'?json_decode($product['image'],true):$product['image']) : [];
                                $product_image = count($images) > 0 ? $images[0] : null;

                            ?>
                            <tr>
                                <td>
                                    {{$order_details->firstItem()+$key}}
                                </td>
                                <td>
                                    <a href="{{route('admin.product.view',[$product['id']])}}" target="_blank" class="product-list-media">
                                        <img src="{{asset('storage/app/public/product')}}/{{$product_image}}"
                                             onerror="this.src='{{asset('/public/assets/admin/img/160x160/2.png')}}'"
                                        />
                                        <h6 class="name line--limit-2">
                                            {{$product['name']}}
                                        </h6>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">{{$detail['quantity']}}</span>
                                </td>
                                <td>
                                    <div class="word-nobreak">
                                        {{date('d M Y',strtotime($detail['created_at']))}}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ Helpers::set_symbol($ord_total) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @php

                        @endphp

                        </tbody>
                    </table>
                    <div class="card-footer border-0">
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            {!! $order_details->links() !!}
                        </div>
                        <!-- End Pagination -->
                    </div>
                    @if(count($order_details) === 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-120px" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                            <p class="mb-0">No data to show</p>
                        </div>
                    @endif
                </div>
                <!-- End Table -->

            </div>
            <!-- End Row -->
        </div>
    </div>
@endsection

@push('script_2')

    <script>
        /*$(document).ready(function () {
            $("#start_date").on("change", function () {
                console.log('start');
                $('#end_date').attr('min',$(this).val());
                $('#end_date').attr('required', true);
            });

            $("#end_date").on("change", function () {
                console.log('end');
                $('#start_date').attr('max',$(this).val());
            });
        });*/

        $('#start_date,#end_date').change(function () {
            let fr = $('#start_date').val();
            let to = $('#end_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#start_date').val('');
                    $('#end_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });

    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('input').addClass('form-control');
        });

    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });
        });
    </script>
@endpush

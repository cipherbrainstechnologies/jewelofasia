@extends('layouts.admin.app')

@section('title', translate('Order List'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header Start -->
        <div class="page-header">
            <h1 class="mb-0 page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/all_orders.png')}}" class="w--20" alt="">
                </span>
                <span class="">
                    @if($status =='processing')
                        {{ translate(ucwords(str_replace('_',' ','Packaging' ))) }} {{translate('Orders')}}
                    @elseif($status =='failed')
                        {{ translate(ucwords(str_replace('_',' ','Failed to Deliver' ))) }} {{translate('Orders')}}
                    @else
                        {{ translate(ucwords(str_replace('_',' ',$status ))) }} {{translate('Orders')}}
                    @endif
                    <span class="badge badge-pill badge-soft-secondary ml-2">{{ $orders->total() }}</span>
                </span>

            </h1>
        </div>
        <!-- Page Header End -->
        <!-- Card -->
        <div class="card">
            <div class="card-header shadow flex-wrap p-20px border-0">
                <h5 class="form-bold w-100 mb-3">{{ translate('Select Date Range') }}</h5>
                <form class="w-100">
                    <div class="row g-3 g-sm-4 g-md-3 g-lg-4">
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <select class="custom-select custom-select-sm text-capitalize min-h-45px" name="branch_id">
                                <option disabled>--- {{translate('select')}} {{translate('branch')}} ---</option>
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
                            <a href="{{ route('admin.orders.list',['all']) }}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                            <button type="submit" id="show_filter_data" class="btn w-100 btn--primary min-h-45px">{{translate('show data')}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Header -->

            @if($status == 'all')
                <div class="p-20px pb-0 mt-4">
                    <div class="row g-3 g-sm-4 g-md-3 g-lg-4">

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['pending'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/pending.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('pending')}}</span>
                                        </h6>
                                        <span class="card-title text-0661CB">
    {{--                                    {{\App\Model\Order::where(['order_status'=>'pending'])->count()}}--}}
                                            {{ $count_data['pending'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['confirmed'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/confirmed.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('confirmed')}}</span>
                                        </h6>
                                        <span class="card-title text-107980">
                                        {{ $count_data['confirmed'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['processing'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/processing.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('packaging')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                        {{ $count_data['processing'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['out_for_delivery'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('/public/assets/admin/img/delivery/out-for-delivery.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('out_for_delivery')}}</span>
                                        </h6>
                                        <span class="card-title text-00B2BE">
                                        {{ $count_data['out_for_delivery'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>



                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['delivered'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/1.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('delivered')}}</span>
                                        </h6>
                                        <span class="card-title text-success">
                                        {{ $count_data['delivered'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>


                            <!-- Static Cancel -->
                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['all'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/2.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('Canceled')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                        {{ $count_data['canceled'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>
                            <!-- Static Cancel -->


                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['returned'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/3.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('returned')}}</span>
                                        </h6>
                                        <span class="card-title text-warning">
                                        {{ $count_data['returned'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['failed'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('public/assets/admin/img/delivery/4.png')}}" alt="dashboard" class="oder--card-icon">
                                            <span>{{translate('failed_to_deliver')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                        {{ $count_data['failed'] }}
                                    </span>
                                    </div>
                                </a>
                            </div>

                        </div>
                </div>
            @endif

            <div class="card-body p-20px">
                <div class="order-top">
                    <div class="card--header">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control"
                                       placeholder="{{translate('Ex : Search by ID, order or payment status')}}" aria-label="Search"
                                       value="{{$search}}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text">
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- Unfold -->
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.orders.export', [$status, 'branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'search'=>Request::get('search')])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        style="width: 100%">
                        <thead class="thead-light">
                        <tr>
                            <th class="">
                                {{translate('#')}}
                            </th>
                            <th class="table-column-pl-0">{{translate('order ID')}}</th>
                            <th>{{translate('Delivery')}} {{translate('date')}}</th>
                            <th>{{translate('Time Slot')}}</th>
                            <th>{{translate('customer')}}</th>
                            <th>{{translate('branch')}}</th>
                            <th>{{translate('total amount')}}</th>
                            <th>
                                <div class="text-center">
                                    {{translate('order')}} {{translate('status')}}
                                </div>
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('order')}} {{translate('type')}}
                                </div>
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('action')}}
                                </div>
                            </th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="">
                                    {{$orders->firstItem()+$key}}
                                </td>
                                <td class="table-column-pl-0">
                                    <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>{{date('d M Y',strtotime($order['delivery_date']))}}</td>
                                <td>
                                    <span>{{$order->time_slot?date(config('time_format'), strtotime($order->time_slot['start_time'])).' - ' .date(config('time_format'), strtotime($order->time_slot['end_time'])) :'No Time Slot'}}</span>
                                </td>
                                <td>
                                    @if($order->is_guest == 0)
                                        @if(isset($order->customer))
                                            <div>
                                                <a class="text-body text-capitalize font-medium"
                                                   href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                            </div>
                                            <div class="text-sm">
                                                <a href="Tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                            </div>
                                        @elseif($order->user_id != null && !isset($order->customer))
                                            <label
                                                class="text-danger">{{translate('Customer_not_available')}}
                                            </label>
                                        @else
                                            <label
                                                class="text-success">{{translate('Walking Customer')}}
                                            </label>
                                        @endif
                                    @else
                                        <label
                                            class="text-success">{{translate('Guest Customer')}}
                                        </label>
                                    @endif

                                </td>
                                <td>
                                    <label class="badge badge-soft-primary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                                </td>

                                <td>
                                    <div class="mw-90">
{{--                                        <div>--}}
{{--                                            {{ Helpers::set_symbol($order['order_amount']) }}--}}
{{--                                        </div>--}}
                                        <div>

                                                <?php
                                                $vat_status = $order->details ? $order->details[0]->vat_status : '';
                                                if($vat_status == 'included'){
                                                    $order_amount = $order['order_amount'] - $order['total_tax_amount'];
                                                }else{
                                                    $order_amount = $order['order_amount'];
                                                }
                                                ?>
                                            {{ Helpers::set_symbol($order_amount) }}
                                        </div>
                                        @if($order->payment_status=='paid')
                                            <span class="text-success">
                                                {{translate('paid')}}
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                {{translate($order['payment_status'])}}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                            {{translate('pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                            {{translate('confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                            {{translate('packaging')}}
                                        </span>
                                    @elseif($order['order_status']=='out_for_delivery')
                                        <span class="badge badge-soft-warning">
                                            {{translate('out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                            {{translate('delivered')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                            {{ translate(str_replace('_',' ',$order['order_status'])) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_type']=='take_away')
                                        <span class="badge badge-soft-info">
                                            {{translate('take_away')}}
                                        </span>
                                    @elseif($order['order_type']=='pos')
                                        <span class="badge badge-soft-info">
                                        {{translate('POS')}}
                                    </span>
                                    @else
                                        <span class="badge badge-soft-success">
                                        {{translate($order['order_type'])}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn btn--primary btn-outline-primary" href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i class="tio-invisible"></i></a>
                                        <a class="action-btn btn-outline-primary-2" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                            <i class="tio-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if(count($orders)==0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                    </div>
                @endif
                <!-- End Table -->
            </div>
            <!-- Footer -->
            <div class="card-footer border-0">
                <!-- Pagination -->
                <div class="d-flex justify-content-center justify-content-sm-end">
                    {!! $orders->links() !!}
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')

    <script>
        function filter_branch_orders(id) {
            location.href = '{{url('/')}}/admin/orders/branch-filter/' + id;
        }
    </script>

    <script>
        $('#from_date').on('change', function () {
            let dateData = $('#from_date').val();
            console.log(dateData);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.orders.date_search')}}',
                data: {
                    'dateData': dateData,
                },

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
        $('#from_time').on('change', function () {
            let timeData = $('#from_time').val();
            let dateData = $('#from_date').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.orders.time_search')}}',
                data: {
                    'timeData': timeData,
                    'dateData': dateData,

                },

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
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

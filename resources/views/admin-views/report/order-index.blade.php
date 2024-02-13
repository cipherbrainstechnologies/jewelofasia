<?php
ini_set('memory_limit', '-1');

?>
@extends('layouts.admin.app')

@section('title', translate('Order Report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="media align-items-center mb-2">
                <!-- Avatar -->
                <div class="">
                    <img src="{{asset('public/assets/admin/img/order-img.png')}}" class="w--20" alt="">
                </div>
                <!-- End Avatar -->

                <div class="media-body pl-3">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0">
                            <h1 class="page-header-title">{{translate('order')}} {{translate('report')}} {{translate('overview')}}</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#"  class="text--primary-2">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>

                                <div class="col-auto">
                                    <div class="row align-items-center g-0">
                                        <div class="col-auto pr-2">{{translate('date :')}}</div>

                                        <!-- Flatpickr -->
                                        <div class="text--primary-2">
                                            {{session('from_date')}} - {{session('to_date')}}
                                        </div>
                                        <!-- End Flatpickr -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Media -->

        </div>
        <!-- End Page Header -->

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('admin.report.set-date')}}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div>
                                <label class="form-label mb-0 font-semibold">{{translate('show')}} {{translate('data')}} {{translate('by')}} {{translate('date')}}
                                    {{translate('range')}}</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="from" id="from_date"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ session('from_date') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('end')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="to" id="to_date"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ session('to_date') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="input-label d-none d-md-block">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn--primary min-h-45px btn-block">{{translate('show')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row g-3 mt-3">

                    @php
                        $from = session('from_date');
                        $to = session('to_date');
                        $total=\App\Model\Order::whereBetween('created_at', [$from, $to])->count();
                        if($total==0){
                        $total=.01;
                        }
                    @endphp
                    <div class="col-sm-6 col-lg-3">
                    @php
                        $delivered=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->count()
                    @endphp
                    <!-- Card -->
                        <div class="card card-sm bg--2 border-0 shadow-none">
                            <div class="card-body py-5 px-xxl-5">
                                <div class="row g-2">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-shopping-cart nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('delivered')}}</h4>
                                                <span class="text-success">
                                                <i class="tio-trending-up"></i> {{$delivered}}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                            "value": {{round(($delivered/$total)*100)}},
                                            "maxValue": 100,
                                            "duration": 2000,
                                            "isViewportInit": true,
                                            "colors": ["#a5e1cb", "#60d3a9"],
                                            "radius": 25,
                                            "width": 3,
                                            "fgStrokeLinecap": "round",
                                            "textFontSize": 14,
                                            "additionalText": "%",
                                            "textClass": "circle-custom-text",
                                            "textColor": "#60d3a9"
                                            }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3">
                    @php
                        $returned=\App\Model\Order::where(['order_status'=>'returned'])->whereBetween('created_at', [$from, $to])->count()
                    @endphp
                    <!-- Card -->
                        <div class="card card-sm bg--3 border-0 shadow-none">
                            <div class="card-body py-5 px-xxl-5">
                                <div class="row g-2">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-shopping-cart-off nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('returned')}}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{$returned}}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{round(($returned/$total)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#ffc5c5", "#ff6e6e"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#ff6e6e"
                                }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3">
                    @php
                        $failed=\App\Model\Order::where(['order_status'=>'failed'])->whereBetween('created_at', [$from, $to])->count()
                    @endphp
                    <!-- Card -->
                        <div class="card card-sm bg--1 border-0 shadow-none">
                            <div class="card-body py-5 px-xxl-5">
                                <div class="row g-2">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-message-failed nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('failed')}}</h4>
                                                <span class="text-danger">
                                                <i class="tio-trending-up"></i> {{$failed}}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{round(($failed/$total)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#abdcff", "#5bbbff"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#5bbbff"
                                }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3">
                    @php
                        $canceled=\App\Model\Order::where(['order_status'=>'canceled'])->whereBetween('created_at', [$from, $to])->count()
                    @endphp
                    <!-- Card -->
                        <div class="card card-sm bg--4 border-0 shadow-none">
                            <div class="card-body py-5 px-xxl-5">
                                <div class="row g-2">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-flight-cancelled nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('canceled')}}</h4>
                                                <span class="text-muted">
                                                <i class="tio-trending-up"></i> {{$canceled}}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{round(($canceled/$total)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#ffd4ae", "#ff7800"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#ff7800"
                                }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stats -->

        <!-- Card -->
        <div class="card mb-3 border-bottom border-right border-left border-top">
            <!-- Header -->
            <div class="card-header">
                @php
                    $x=1;
                    $y=12;
                    $total=\App\Model\Order::whereBetween('created_at', [date('Y-'.$x.'-01'), date('Y-'.$y.'-30')])->count()
                @endphp
                <h6 class="card-subtitle mb-0">{{translate('total')}} {{translate('orders')}} of {{date('Y')}}: <span
                        class="h3 ml-sm-2">{{round($total)}}</span>
                </h6>

            </div>
            <!-- End Header -->

        @php
            $delivered=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $delivered[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->count();
                }
        @endphp

        @php
            $ret=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $ret[$i]=\App\Model\Order::where(['order_status'=>'returned'])->whereBetween('created_at', [$from, $to])->count();
                }
        @endphp

        @php
            $fai=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $fai[$i]=\App\Model\Order::where(['order_status'=>'failed'])->whereBetween('created_at', [$from, $to])->count();
                }
        @endphp

        @php
            $can=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $can[$i]=\App\Model\Order::where(['order_status'=>'canceled'])->whereBetween('created_at', [$from, $to])->count();
                }
        @endphp

        <!-- Body -->
            <div class="card-body">
                <!-- Bar Chart -->
                <div class="chartjs-custom h-18rem">
                    <canvas class="js-chart"
                            data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                           "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                           "datasets": [{
                            "data": [{{$delivered[1]}},{{$delivered[2]}},{{$delivered[3]}},{{$delivered[4]}},{{$delivered[5]}},{{$delivered[6]}},{{$delivered[7]}},{{$delivered[8]}},{{$delivered[9]}},{{$delivered[10]}},{{$delivered[11]}},{{$delivered[12]}}],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#60d3a9",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#60d3a9",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#60d3a9"
                          },
                          {
                            "data": [{{$ret[1]}},{{$ret[2]}},{{$ret[3]}},{{$ret[4]}},{{$ret[5]}},{{$ret[6]}},{{$ret[7]}},{{$ret[8]}},{{$ret[9]}},{{$ret[10]}},{{$ret[11]}},{{$ret[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#ff6e6e",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#ff6e6e",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#ff6e6e"
                          },
                          {
                            "data": [{{$fai[1]}},{{$fai[2]}},{{$fai[3]}},{{$fai[4]}},{{$fai[5]}},{{$fai[6]}},{{$fai[7]}},{{$fai[8]}},{{$fai[9]}},{{$fai[10]}},{{$fai[11]}},{{$fai[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "darkred",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#00c9db",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#00c9db"
                          },
                          {
                            "data": [{{$can[1]}},{{$can[2]}},{{$can[3]}},{{$can[4]}},{{$can[5]}},{{$can[6]}},{{$can[7]}},{{$can[8]}},{{$can[9]}},{{$can[10]}},{{$can[11]}},{{$can[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "gray",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#ff7800",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#ff7800"
                          }]
                        },
                        "options": {
                          "gradientPosition": {"y1": 200},
                           "scales": {
                              "yAxes": [{
                                "gridLines": {
                                  "color": "#e7eaf3",
                                  "drawBorder": false,
                                  "zeroLineColor": "#e7eaf3"
                                },
                                "ticks": {
                                  "min": 0,
                                  "max": {{\App\CentralLogics\Helpers::max_orders()}},
                                  "stepSize": {{round(\App\CentralLogics\Helpers::max_orders()/4)}},
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 10,
                                  "postfix": ""
                                }
                              }],
                              "xAxes": [{
                                "gridLines": {
                                  "display": false,
                                  "drawBorder": false
                                },
                                "ticks": {
                                  "fontSize": 12,
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 5
                                }
                              }]
                          },
                          "tooltips": {
                            "prefix": "",
                            "postfix": "",
                            "hasIndicator": true,
                            "mode": "index",
                            "intersect": false,
                            "lineMode": true,
                            "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                          },
                          "hover": {
                            "mode": "nearest",
                            "intersect": true
                          }
                        }
                      }'>
                    </canvas>
                </div>
                <!-- End Bar Chart -->
            </div>
            <!-- End Body -->
        </div>
        <!-- End Card -->

        <div class="row">
            <div class="col-lg-12 mb-3 mb-lg-12">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Header -->
                    <div class="card-header flex-wrap __gap-2">
                        <h4 class="card-header-title">{{translate('weekly')}} {{translate('report')}}</h4>

                        <!-- Nav -->
                        <ul class="nav nav-segment" id="eventsTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="this-week-tab" data-toggle="tab" href="#this-week"
                                   role="tab">
                                    {{translate('this')}} {{translate('week')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="last-week-tab" data-toggle="tab" href="#last-week" role="tab">
                                    {{translate('last')}} {{translate('week')}}
                                </a>
                            </li>
                        </ul>
                        <!-- End Nav -->
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body card-body-height">
                    @php
                        $orders= \App\Model\Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
                    @endphp
                    <!-- Tab Content -->
                        <div class="tab-content" id="eventsTabContent">
                            <div class="tab-pane fade show active" id="this-week" role="tabpanel"
                                 aria-labelledby="this-week-tab">
                                <!-- Card -->
                                @foreach($orders as $order)
                                    <a class="card card-border-left border-left-primary shadow-none rounded-0"
                                       href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                        <div class="card-body py-0">
                                            <div class="row">
                                                <div class="col-sm mb-2 mb-sm-0">
                                                    <h2 class="font-weight-normal mb-1">#{{$order['id']}} <small
                                                            class="text-body text-uppercase">{{translate('id')}}</small>
                                                    </h2>
                                                    <h5 class="text-hover-primary mb-0">{{translate('order')}} {{translate('amount')}}
                                                        : {{ Helpers::set_symbol($order['order_amount']) }}</h5>
                                                    <small
                                                        class="text-body">{{date('d M Y',strtotime($order['created_at']))}}</small>
                                                </div>

                                                <div class="col-sm-auto align-self-sm-end">
                                                    <!-- Avatar Group -->
                                                    <div class="">
                                                        {{translate('status')}} <strong> : {{$order['order_status'] == 'processing' ? translate('packaging') : translate($order['order_status'])}} <br></strong>
                                                    </div>
                                                    <!-- End Avatar Group -->
                                                </div>
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                    </a>
                                    <!-- End Card -->
                                    <hr>
                                @endforeach
                            </div>

                            @php
                                $orders= \App\Model\Order::whereBetween('created_at', [now()->subDays(7)->startOfWeek(), now()->subDays(7)->endOfWeek()])->get();
                            @endphp

                            <div class="tab-pane fade" id="last-week" role="tabpanel" aria-labelledby="last-week-tab">
                                @foreach($orders as $order)
                                    <a class="card card-border-left border-left-primary shadow-none rounded-0"
                                       href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                        <div class="card-body py-0">
                                            <div class="row">
                                                <div class="col-sm mb-2 mb-sm-0">
                                                    <h2 class="font-weight-normal mb-1">#{{$order['id']}} <small
                                                            class="text-body text-uppercase">{{translate('id')}}</small>
                                                    </h2>
                                                    <h5 class="text-hover-primary mb-0">{{translate('order')}} {{translate('amount')}}
                                                        : {{ Helpers::set_symbol($order['order_amount']) }}</h5>
                                                    <small
                                                        class="text-body">{{date('d M Y',strtotime($order['created_at']))}}</small>
                                                </div>

                                                <div class="col-sm-auto align-self-sm-end">
                                                    <!-- Avatar Group -->
                                                    <div class="">
                                                        {{translate('status')}} <strong> : {{$order['order_status'] == 'processing' ? translate('packaging') : translate($order['order_status'])}} <br></strong>
                                                    </div>
                                                    <!-- End Avatar Group -->
                                                </div>
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                    </a>
                                    <!-- End Card -->
                                    <hr>
                                @endforeach
                            </div>
                        </div>
                        <!-- End Tab Content -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>
@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script
        src="{{asset('public/assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/js/hs.chartjs-matrix.js"></script>

    <script>
        $(document).on('ready', function () {

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CHARTJS
            // =======================================================
            $('.js-chart').each(function () {
                $.HSCore.components.HSChartJS.init($(this));
            });

            var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            // Call when tab is clicked
            $('[data-toggle="chart"]').click(function (e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                // Update datasets for chart
                updatingChart.data.datasets.forEach(function (dataset, key) {
                    dataset.data = updatingChartDatasets[keyDataset][key];
                });
                updatingChart.update();
            })


            // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
            // =======================================================
            function generateHoursData() {
                var data = [];
                var dt = moment().subtract(365, 'days').startOf('day');
                var end = moment().startOf('day');
                while (dt <= end) {
                    data.push({
                        x: dt.format('YYYY-MM-DD'),
                        y: dt.format('e'),
                        d: dt.format('YYYY-MM-DD'),
                        v: Math.random() * 24
                    });
                    dt = dt.add(1, 'day');
                }
                return data;
            }

            $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                data: {
                    datasets: [{
                        label: 'Commits',
                        data: generateHoursData(),
                        width: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function () {
                                return '';
                            },
                            label: function (item, data) {
                                var v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                } else {
                                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                }
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            position: 'bottom',
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'week',
                                round: 'week',
                                displayFormats: {
                                    week: 'MMM'
                                }
                            },
                            ticks: {
                                "labelOffset": 20,
                                "maxRotation": 0,
                                "minRotation": 0,
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 12,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'day',
                                parser: 'e',
                                displayFormats: {
                                    day: 'ddd'
                                }
                            },
                            ticks: {
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 2,
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function () {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            // INITIALIZATION OF CIRCLES
            // =======================================================
            $('.js-circle').each(function () {
                var circle = $.HSCore.components.HSCircles.init($(this));
            });
        });
    </script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush

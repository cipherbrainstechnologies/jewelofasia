@extends('layouts.admin.app')

@section('title', translate('Earning Report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="media align-items-center mb-2">
                <!-- Avatar -->
                <div class="">
                    <img src="{{asset('public/assets/admin/img/image-4.png')}}" class="w--20" alt="">
                </div>
                <!-- End Avatar -->

                <div class="media-body pl-3">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0 text-capitalize">
                            <h1 class="page-header-title">{{translate('earning')}} {{translate('report')}} {{translate('overview')}}</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#"  class="text--primary-2">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>

                                <div class="col-auto">
                                    <div class="row align-items-center g-0 m-0">
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

        <div class="card">
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
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('end')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="to" id="to_date"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
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
                        $total_tax=\App\Model\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('total_tax_amount');

                        if($total_tax==0){
                            $total_tax=0.01;
                        }
                        //dd($total_tax);

                        $total_delivery_charge=\App\Model\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('delivery_charge');

                        if($total_delivery_charge==0){
                            $total_delivery_charge=0.01;
                        }

                        $total_sold=\App\Model\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('order_amount');

                        if($total_sold==0){
                            $total_sold=.01;
                        }

                        $total_earning = $total_sold - $total_tax - $total_delivery_charge;
                    @endphp
                    <div class="col-sm-6">

                    <!-- Card -->
                        <div class="card card-sm bg--2 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-dollar-outlined nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('sold')}}</h4>
                                                <span class="text-success">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($total_sold))) }}
                                                </span>
                                            </div>

                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                            "value": {{$total_sold=='.01'?0:round(($total_sold/$total_sold)*100)}},
                                            "maxValue": 100,
                                            "duration": 2000,
                                            "isViewportInit": true,
                                            "colors": ["#00800040", "green"],
                                            "radius": 25,
                                            "width": 3,
                                            "fgStrokeLinecap": "round",
                                            "textFontSize": 14,
                                            "additionalText": "%",
                                            "textClass": "circle-custom-text",
                                            "textColor": "green"
                                            }'>
                                        </div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6">
                        <!-- Card -->
                        <div class="card card-sm bg--3 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('tax')}}</h4>
                                                <span class="text-danger">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($total_tax))) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$total_tax=='0.01'?0:round(((abs($total_tax))/$total_sold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#f83b3b40", "#f83b3b"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#f83b3b"
                                }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>
                    <div class="col-sm-6">
                        <!-- Card -->
                        <div class="card card-sm bg--4 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('delivery')}} {{translate('charge') }}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($total_delivery_charge))) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$total_delivery_charge=='0.01'?0:round(((abs($total_delivery_charge))/$total_sold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#ec9a3c40", "#ec9a3c"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#ec9a3c"
                                }'></div>
                                        <!-- End Circle -->
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Card -->
                    </div>
                    <div class="col-sm-6">
                        <!-- Card -->
                        <div class="card card-sm bg--1 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <!-- Media -->
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('earning')}}</h4>
                                                <span class="text-warning" style="color: #0096ff !important">
                                                <i class="tio-trending-up"></i> {{ Helpers::set_symbol(round(abs($total_earning))) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- End Media -->
                                    </div>

                                    <div class="col-auto">
                                        <!-- Circle -->
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$total_earning=='0.01'?0:round(((abs($total_earning))/$total_sold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#0096ff40", "#0096ff90"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#0096ff"
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


        <div class="card mt-3">
            <!-- Header -->
            <div class="card-header">
                @php
                    $total_sold=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [date('y-01-01'), date('y-12-31')])->sum('order_amount')
                @endphp
                <h6 class="card-subtitle mb-0">{{ translate('Total sale of ') }} {{date('Y')}} :<span
                        class="h3 ml-sm-2"> {{ Helpers::set_symbol($total_sold) }}</span>
                </h6>

            </div>
            <!-- End Header -->

        @php
            $sold=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $sold[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
                }
        @endphp

        @php
            $tax=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $tax[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('total_tax_amount');
                }
        @endphp

        @php
            $delivery_charge=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $delivery_charge[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('delivery_charge');
                }
        @endphp

        @php
            $sold_cal=[];
            $tax_cal=[];
            $delivery_charge_cal=[];
            $earning=[];
            for ($i=1;$i<=12;$i++){
                $from = date('Y-'.$i.'-01');
                $to = date('Y-'.$i.'-30');
                $sold_cal[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
                $tax_cal[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('total_tax_amount');
                $delivery_charge_cal[$i]=\App\Model\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('delivery_charge');
                $earning[$i] = $sold_cal[$i] - $tax_cal[$i] - $delivery_charge_cal[$i];
            }
        @endphp

        @php($currency_position = Helpers::get_business_settings('currency_symbol_position'))

        <!-- Body -->
            <div class="card-body">
                <!-- Bar Chart -->
                <div class="chartjs-custom" style="height: 18rem;">
                    <canvas class="js-chart"
                            data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                           "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                           "datasets": [{
                            "data": [{{$sold[1]}},{{$sold[2]}},{{$sold[3]}},{{$sold[4]}},{{$sold[5]}},{{$sold[6]}},{{$sold[7]}},{{$sold[8]}},{{$sold[9]}},{{$sold[10]}},{{$sold[11]}},{{$sold[12]}}],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "green",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "green",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#377dff"
                          },

                          {
                            "data": [{{$tax[1]}},{{$tax[2]}},{{$tax[3]}},{{$tax[4]}},{{$tax[5]}},{{$tax[6]}},{{$tax[7]}},{{$tax[8]}},{{$tax[9]}},{{$tax[10]}},{{$tax[11]}},{{$tax[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#f83b3b",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#f83b3b",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#f83b3b"
                          },
                          {
                            "data": [{{$delivery_charge[1]}},{{$delivery_charge[2]}},{{$delivery_charge[3]}},{{$delivery_charge[4]}},{{$delivery_charge[5]}},{{$delivery_charge[6]}},{{$delivery_charge[7]}},{{$delivery_charge[8]}},{{$delivery_charge[9]}},{{$delivery_charge[10]}},{{$delivery_charge[11]}},{{$delivery_charge[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#f5a200",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#f5a200",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#f5a200"
                          },
                          {
                            "data": [{{$earning[1]}},{{$earning[2]}},{{$earning[3]}},{{$earning[4]}},{{$earning[5]}},{{$earning[6]}},{{$earning[7]}},{{$earning[8]}},{{$earning[9]}},{{$earning[10]}},{{$earning[11]}},{{$earning[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#0096ff",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#0096ff",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#0096ff"
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
                                  "max": {{\App\CentralLogics\Helpers::max_earning()}},
                                  "stepSize": {{round(\App\CentralLogics\Helpers::max_earning()/5)}},
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 10,
                                  "{{ $currency_position == 'left' ? 'prefix' : 'postfix'}}": " {{\App\CentralLogics\Helpers::currency_symbol()}}"
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

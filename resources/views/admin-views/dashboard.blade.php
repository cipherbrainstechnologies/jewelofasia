@extends('layouts.admin.app')

@section('title', translate('Dashboard'))

@section('content')
    @if(Helpers::module_permission_check(MANAGEMENT_SECTION['dashboard_management']))
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header mb-0 pb-2 border-0">
                <h1 class="page-header-title text-107980">{{ translate('welcome')}}, {{auth('admin')->user()->f_name}}</h1>
                <p class="welcome-msg">{{ translate('welcome_message')}}</p>
            </div>
            <!-- End Page Header -->

            <!-- Card -->
            <div class="card mb-10px">
                <div class="card-body">
                    <div class="btn--container justify-content-between align-items-center mb-2 pb-1">
                        <h5 class="card-title mb-2">
                            <img src="{{asset('/public/assets/admin/img/business-analytics.png')}}" alt="" class="card-icon">
                            {{ translate('Business Analytics') }}
                        </h5>
                        <div class="mb-2">
                            <select class="custom-select" name="statistics_type"
                                    onchange="order_stats_update(this.value)">
                                <option
                                    value="overall" {{session()->has('statistics_type') && session('statistics_type') == 'overall'?'selected':''}}>
                                    {{ translate('Overall Statistics') }}
                                </option>
                                <option
                                    value="today" {{session()->has('statistics_type') && session('statistics_type') == 'today'?'selected':''}}>
                                    {{ translate("Today's Statistics") }}
                                </option>
                                <option
                                    value="this_month" {{session()->has('statistics_type') && session('statistics_type') == 'this_month'?'selected':''}}>
                                    {{ translate("This Month's Statistics") }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2" id="order_stats">
                        @include('admin-views.partials._dashboard-order-stats',['data'=>$data])
                    </div>
                </div>
            </div>
            <!-- End Card -->

            {{-- <div class="row gx-2 gx-lg-3 mb-3 mb-lg-5">
                <div class="col-lg-12">

                    <!-- Card -->
                    <div class="card h-100">
                        <!-- Body -->
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12 mb-3 border-bottom">
                                    <h5 class="card-header-title float-left mb-2">
                                        <i style="font-size: 30px" class="tio-chart-pie-1"></i>
                                        {{ translate('Earning statistics for business analytics') }}
                                    </h5>
                                    <!-- Legend Indicators -->
                                    <h5 class="card-header-title float-right mb-2">{{ translate('Monthly Earning') }}
                                        <i style="font-size: 30px" class="tio-chart-bar-2"></i>
                                    </h5>
                                    <!-- End Legend Indicators -->
                                </div>
                                <div class="col-md-4 graph-border-1">
                                    <div class="mt-2 center-div">
                                          <span class="h6 mb-0">
                                              <i class="legend-indicator" style="background-color: #B6C867!important;"></i>
                                             {{ translate('earnings') }} : {{ Helpers::set_symbol(array_sum($earning)) }}
                                          </span>
                                    </div>
                                </div>
                            </div>
                            <!-- End Row -->

                            <!-- Bar Chart -->
                            <div class="chartjs-custom">
                                <canvas id="updatingData" style="height: 20rem;"
                                        data-hs-chartjs-options='{
                                "type": "bar",
                                "data": {
                                  "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                                  "datasets": [
                                 {
                                    "data": [{{$earning[1]}},{{$earning[2]}},{{$earning[3]}},{{$earning[4]}},{{$earning[5]}},{{$earning[6]}},{{$earning[7]}},{{$earning[8]}},{{$earning[9]}},{{$earning[10]}},{{$earning[11]}},{{$earning[12]}}],
                                    "backgroundColor": "#B6C867",
                                    "borderColor": "#B6C867"
                                  }]
                                },
                                "options": {
                                  "scales": {
                                    "yAxes": [{
                                      "gridLines": {
                                        "color": "#e7eaf3",
                                        "drawBorder": false,
                                        "zeroLineColor": "#e7eaf3"
                                      },
                                      "ticks": {
                                        "beginAtZero": true,
                                        "stepSize": 50000,
                                        "fontSize": 12,
                                        "fontColor": "#97a4af",
                                        "fontFamily": "Open Sans, sans-serif",
                                        "padding": 10,
                                        "postfix": " {{ \App\CentralLogics\Helpers::currency_symbol() }}"
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
                                      },
                                      "categoryPercentage": 0.5,
                                      "maxBarThickness": "10"
                                    }]
                                  },
                                  "cornerRadius": 2,
                                  "tooltips": {
                                    "prefix": " ",
                                    "hasIndicator": true,
                                    "mode": "index",
                                    "intersect": false
                                  },
                                  "hover": {
                                    "mode": "nearest",
                                    "intersect": true
                                  }
                                }
                              }'></canvas>
                            </div>
                            <!-- End Bar Chart -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </div>
            </div> --}}
            <!-- End Row -->


            <!-- Dashboard Statistics -->
            <div class="dashboard-statistics">
                <div class="row g-1">
                    <!-- Order Statistics -->
                    <div class="col-lg-8 col--xl-8">
                        <div class="card h-100 bg-white">
                            <div class="card-body p-20px pb-0">
                                <div class="btn--container justify-content-between align-items-center">
                                    <h5 class="card-title mb-2">
                                        <img src="{{asset('/public/assets/admin/img/order-statistics.png')}}" alt=""
                                             class="card-icon">
                                        <span>{{translate('order_statistics')}}</span>
                                    </h5>
                                    <div class="mb-2">
                                        <div class="d-flex flex-wrap statistics-btn-grp">
                                            <label>
                                                <input type="radio" name="order__statistics" hidden checked>
                                                <span data-order-type="yearOrder"
                                                      onclick="orderStatisticsUpdate(this)">{{translate('This_Year')}}</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="order__statistics" hidden>
                                                <span data-order-type="MonthOrder"
                                                      onclick="orderStatisticsUpdate(this)">{{translate('This_Month')}}</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="order__statistics" hidden>
                                                <span data-order-type="WeekOrder"
                                                      onclick="orderStatisticsUpdate(this)">{{translate('This Week')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="updatingOrderData">
                                    <div id="line-chart-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Order Statistics -->
                    <!-- Dognut Pie -->
                    <div class="col-lg-4 col--xl-4">
                        <div class="card h-100 bg-white">
                            <div class="card-header border-0 order-header-shadow">
                                <h5 class="card-title">
                                    <span>{{translate('order_status_statistics')}}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="position-relative pie-chart">
                                    <div id="dognut-pie"></div>
                                    <!-- Total Orders -->
                                    <div class="total--orders">
                                        <h3>{{$data['pending_count'] + $data['ongoing_count'] + $data['delivered_count']+ $data['canceled_count']+ $data['returned_count']+ $data['failed_count']}} </h3>
                                        <span>{{ translate('orders') }}</span>
                                    </div>
                                    <!-- Total Orders -->
                                </div>
                                <div class="apex-legends">
                                    <div class="before-bg-E5F5F1">
                                        <span>{{ translate('pending') }} ({{$data['pending_count']}})</span>
                                    </div>
                                    <div class="before-bg-036BB7">
                                        <span>{{ translate('ongoing') }} ({{$data['ongoing_count']}})</span>
                                    </div>
                                    <div class="before-bg-107980">
                                        <span>{{ translate('delivered') }} ({{$data['delivered_count']}})</span>
                                    </div>
                                    <div class="before-bg-0e0def">
                                        <span>{{ translate('canceled') }} ({{$data['canceled_count']}})</span>
                                    </div>
                                    <div class="before-bg-ff00ff">
                                        <span>{{ translate('returned') }} ({{$data['returned_count']}})</span>
                                    </div>
                                    <div class="before-bg-f51414">
                                        <span>{{ translate('failed') }} ({{$data['failed_count']}})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Dognut Pie -->
                    <!-- Earning Statistics -->
                    <div class="col-lg-8 col--xl-8">
                        <div class="card h-100 bg-white">
                            <div class="card-body p-20px pb-0">
                                <div class="btn--container justify-content-between align-items-center">
                                    <h5 class="card-title mb-2">
                                        <img src="{{asset('/public/assets/admin/img/order-statistics.png')}}" alt=""
                                             class="card-icon">
                                        <span>{{translate('earning_statistics')}}</span>
                                    </h5>
                                    <div class="mb-2">
                                        <div class="d-flex flex-wrap statistics-btn-grp">
                                            <label>
                                                <input type="radio" name="earning__statistics" hidden checked>
                                                <span data-earn-type="yearEarn"
                                                      onclick="earningStatisticsUpdate(this)">{{translate('This_Year')}}</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="earning__statistics" hidden>
                                                <span data-earn-type="MonthEarn"
                                                      onclick="earningStatisticsUpdate(this)">{{translate('This_Month')}}</span>
                                            </label>
                                            <label>
                                                <input type="radio" name="earning__statistics" hidden>
                                                <span data-earn-type="WeekEarn"
                                                      onclick="earningStatisticsUpdate(this)">{{translate('This Week')}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="updatingData">
                                    <div id="line-adwords"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Earning Statistics -->
                    <!-- Recent Orders -->
                    <div class="col-lg-4 col--xl-4">
                        <div class="card h-100 bg-white">
                            <div class="card-header border-0 order-header-shadow">
                                <h5 class="card-title d-flex justify-content-between flex-grow-1">
                                    <span>{{translate('recent_orders')}}</span>
                                    <a href="{{route('admin.orders.list',['all'])}}"
                                       class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
                                </h5>
                            </div>
                            <div class="card-body p-10px">
                                <ul class="recent--orders">
                                    @foreach($data['recent_orders'] as $order)
                                        <li>
                                            <a href="{{route('admin.orders.details', ['id'=>$order['id']])}}">
                                                <div>
                                                    <h6>{{translate('order')}} #{{$order['id']}}</h6>
                                                    <span
                                                        class="text-uppercase">{{date('m-d-Y  h:i A', strtotime($order['created_at']))}}</span>
                                                </div>
                                                @if($order['order_status'] == 'pending')
                                                    <span
                                                        class="status text-0661cb">{{translate($order['order_status'])}}</span>
                                                @elseif($order['order_status'] == 'delivered')
                                                    <span
                                                        class="status text-56b98f">{{translate($order['order_status'])}}</span>
                                                @elseif($order['order_status'] == 'confirmed' || $order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery')
                                                    <span
                                                        class="status text-F5A200">{{$order['order_status'] == 'processing' ? translate('packaging') : translate($order['order_status'])}}</span>
                                                @elseif($order['order_status'] == 'canceled' || $order['order_status'] == 'failed')
                                                    <span
                                                        class="status text-F5A200">{{translate($order['order_status'])}}</span>
                                                @else
                                                    <span
                                                        class="status text-0661CB">{{translate($order['order_status'])}}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Orders -->

                    <!-- Top Selling Products -->
                    <div class="col-lg-4">
                        <div class="card h-100">
                            @include('admin-views.partials._top-selling-products',['top_sell'=>$data['top_sell']])
                        </div>
                    </div>
                    <!-- Top Selling Products -->

                    <!-- Top Rated Products -->
                    <div class="col-lg-4">
                        <div class="card h-100">
                            @include('admin-views.partials._most-rated-products',['most_rated_products'=>$data['most_rated_products']])
                        </div>
                    </div>
                    <!-- Top Rated Products -->

                    <!-- Top Customer -->
                    <div class="col-lg-4">
                        <div class="card h-100">
                            @include('admin-views.partials._top-customer',['top_customer'=>$data['top_customer']])
                        </div>
                    </div>
                    <!-- Top Customer -->

                </div>

                <!-- Dashboard Statistics -->


            </div>

            @endif
            @endsection

            @push('script')
                <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
                <script
                    src="{{asset('public/assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
                <script
                    src="{{asset('public/assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

                <!-- Apex Charts -->
                <script src="{{asset('/public/assets/admin/js/apex-charts/apexcharts.js')}}"></script>
                <!-- Apex Charts -->
            @endpush


            @push('script_2')

                <!-- Apex Chart Initialize Start -->

                <!-- Orders Statistics Charts -->
                <script>

                    var options = {
                        series: [{
                            name: "{{ translate('Orders') }}",
                            data: [
                                {{$order_statistics_chart[1]}}, {{$order_statistics_chart[2]}}, {{$order_statistics_chart[3]}}, {{$order_statistics_chart[4]}},
                                {{$order_statistics_chart[5]}}, {{$order_statistics_chart[6]}}, {{$order_statistics_chart[7]}}, {{$order_statistics_chart[8]}},
                                {{$order_statistics_chart[9]}}, {{$order_statistics_chart[10]}}, {{$order_statistics_chart[11]}}, {{$order_statistics_chart[12]}}
                            ],
                        }],
                        chart: {
                            height: 316,
                            type: 'line',
                            zoom: {
                                enabled: false
                            },
                            toolbar: {
                                show: false,
                            },
                            markers: {
                                size: 5,
                            }
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        colors: ['#87bcbf', '#107980'],
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                        },
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        },
                        grid: {
                            show: true,
                            padding: {
                                bottom: 0
                            },
                            borderColor: "#d9e7ef",
                            strokeDashArray: 7,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        yaxis: {
                            tickAmount: 4,
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#line-chart-1"), options);
                    chart.render();
                </script>
                <!-- Orders Statistics Charts -->

                <!-- Dognut Pie Chart -->
                <script>
                    var options = {
                        series: [{{$data['ongoing_count']}}, {{$data['delivered_count']}}, {{$data['pending_count']}}, {{$data['canceled']}}, {{$data['returned']}}, {{$data['failed']}}],
                        chart: {
                            width: 320,
                            type: 'donut',
                        },
                        labels: ['{{ translate('ongoing') }}', '{{ translate('delivered') }}', '{{ translate('pending') }}', '{{translate('canceled')}}', '{{translate('returned')}}', '{{translate('failed')}}'],
                        dataLabels: {
                            enabled: false,
                            style: {
                                colors: ['#036BB7', '#107980', '#6a5acd', '#ff00ff', '#0e0def', '#f51414']
                            }
                        },
                        responsive: [{
                            breakpoint: 1650,
                            options: {
                                chart: {
                                    width: 250
                                },
                            }
                        }],
                        colors: ['#036BB7', '#107980', '#6a5acd', '#0e0def', '#ff00ff', '#f51414'],
                        fill: {
                            colors: ['#036BB7', '#107980', '#6a5acd', '#0e0def', '#ff00ff', '#f51414']
                        },
                        legend: {
                            show: false
                        },
                    };

                    var chart = new ApexCharts(document.querySelector("#dognut-pie"), options);
                    chart.render();

                </script>
                <!-- Dognut Pie Chart -->

                <!-- Earning Statistics Chart -->
                <script>
                    var optionsLine = {
                        chart: {
                            height: 328,
                            type: 'line',
                            zoom: {
                                enabled: false
                            },
                            toolbar: {
                                show: false,
                            },
                        },
                        stroke: {
                            curve: 'straight',
                            width: 2
                        },
                        colors: ['#87bcbf', '#107980'],
                        series: [{
                            name: "{{ translate('Earning') }}",
                            data: [{{$earning[1]}}, {{$earning[2]}}, {{$earning[3]}}, {{$earning[4]}}, {{$earning[5]}}, {{$earning[6]}}, {{$earning[7]}}, {{$earning[8]}}, {{$earning[9]}}, {{$earning[10]}}, {{$earning[11]}}, {{$earning[12]}}],
                        },
                        ],
                        markers: {
                            size: 6,
                            strokeWidth: 0,
                            hover: {
                                size: 9
                            }
                        },
                        grid: {
                            show: true,
                            padding: {
                                bottom: 0
                            },
                            borderColor: "#d9e7ef",
                            strokeDashArray: 7,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        xaxis: {
                            tooltip: {
                                enabled: false
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            offsetY: -20
                        }
                    }
                    var chartLine = new ApexCharts(document.querySelector('#line-adwords'), optionsLine);
                    chartLine.render();
                </script>
                <!-- Earning Statistics Chart -->

                <!-- Apex Chart Initialize End -->


                <script>
                    function order_stats_update(type) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: "{{route('admin.order-stats')}}",
                            type: "post",
                            data: {
                                statistics_type: type,
                            },
                            beforeSend: function () {
                                $('#loading').show()
                            },
                            success: function (data) {
                                $('#order_stats').html(data.view)
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            },
                            complete: function () {
                                $('#loading').hide()
                            }
                        });
                    }
                </script>

                <script>
                    // INITIALIZATION OF CHARTJS
                    // =======================================================
                    Chart.plugins.unregister(ChartDataLabels);

                    $('.js-chart').each(function () {
                        $.HSCore.components.HSChartJS.init($(this));
                    });

                    var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

                    // CALL WHEN TAB IS CLICKED
                    // =======================================================
                    $('[data-toggle="chart-bar"]').click(function (e) {
                        let keyDataset = $(e.currentTarget).attr('data-datasets')

                        if (keyDataset === 'lastWeek') {
                            updatingChart.data.labels = ["Apr 22", "Apr 23", "Apr 24", "Apr 25", "Apr 26", "Apr 27", "Apr 28", "Apr 29", "Apr 30", "Apr 31"];
                            updatingChart.data.datasets = [
                                {
                                    "data": [120, 250, 300, 200, 300, 290, 350, 100, 125, 320],
                                    "backgroundColor": "#377dff",
                                    "hoverBackgroundColor": "#377dff",
                                    "borderColor": "#377dff"
                                },
                                {
                                    "data": [250, 130, 322, 144, 129, 300, 260, 120, 260, 245, 110],
                                    "backgroundColor": "#e7eaf3",
                                    "borderColor": "#e7eaf3"
                                }
                            ];
                            updatingChart.update();
                        } else {
                            updatingChart.data.labels = ["May 1", "May 2", "May 3", "May 4", "May 5", "May 6", "May 7", "May 8", "May 9", "May 10"];
                            updatingChart.data.datasets = [
                                {
                                    "data": [200, 300, 290, 350, 150, 350, 300, 100, 125, 220],
                                    "backgroundColor": "#377dff",
                                    "hoverBackgroundColor": "#377dff",
                                    "borderColor": "#377dff"
                                },
                                {
                                    "data": [150, 230, 382, 204, 169, 290, 300, 100, 300, 225, 120],
                                    "backgroundColor": "#e7eaf3",
                                    "borderColor": "#e7eaf3"
                                }
                            ]
                            updatingChart.update();
                        }
                    })


                    // INITIALIZATION OF BUBBLE CHARTJS WITH DATALABELS PLUGIN
                    // =======================================================
                    $('.js-chart-datalabels').each(function () {
                        $.HSCore.components.HSChartJS.init($(this), {
                            plugins: [ChartDataLabels],
                            options: {
                                plugins: {
                                    datalabels: {
                                        anchor: function (context) {
                                            var value = context.dataset.data[context.dataIndex];
                                            return value.r < 20 ? 'end' : 'center';
                                        },
                                        align: function (context) {
                                            var value = context.dataset.data[context.dataIndex];
                                            return value.r < 20 ? 'end' : 'center';
                                        },
                                        color: function (context) {
                                            var value = context.dataset.data[context.dataIndex];
                                            return value.r < 20 ? context.dataset.backgroundColor : context.dataset.color;
                                        },
                                        font: function (context) {
                                            var value = context.dataset.data[context.dataIndex],
                                                fontSize = 25;

                                            if (value.r > 50) {
                                                fontSize = 35;
                                            }

                                            if (value.r > 70) {
                                                fontSize = 55;
                                            }

                                            return {
                                                weight: 'lighter',
                                                size: fontSize
                                            };
                                        },
                                        offset: 2,
                                        padding: 0
                                    }
                                }
                            },
                        });
                    });
                </script>
                <script>
                    function orderStatisticsUpdate(t) {
                        let value = $(t).attr('data-order-type');

                        $.ajax({
                            url: '{{route('admin.dashboard.order-statistics')}}',
                            type: 'GET',
                            data: {
                                type: value
                            },
                            beforeSend: function () {
                                $('#loading').show()
                            },
                            success: function (response_data) {
                                console.log(response_data);
                                document.getElementById("line-chart-1").remove();
                                let graph = document.createElement('div');
                                graph.setAttribute("id", "line-chart-1");
                                document.getElementById("updatingOrderData").appendChild(graph);

                                var options = {
                                    series: [{
                                        name: "{{ translate('Orders') }}",
                                        data: response_data.orders,
                                    }],
                                    chart: {
                                        height: 316,
                                        type: 'line',
                                        zoom: {
                                            enabled: false
                                        },
                                        toolbar: {
                                            show: false,
                                        },
                                        markers: {
                                            size: 5,
                                        }
                                    },
                                    dataLabels: {
                                        enabled: false,
                                    },
                                    colors: ['#87bcbf', '#107980'],
                                    stroke: {
                                        curve: 'smooth',
                                        width: 3,
                                    },
                                    xaxis: {
                                        categories: response_data.orders_label,
                                    },
                                    grid: {
                                        show: true,
                                        padding: {
                                            bottom: 0
                                        },
                                        borderColor: "#d9e7ef",
                                        strokeDashArray: 7,
                                        xaxis: {
                                            lines: {
                                                show: true
                                            }
                                        }
                                    },
                                    yaxis: {
                                        tickAmount: 4,
                                    }
                                };

                                var chart = new ApexCharts(document.querySelector("#line-chart-1"), options);
                                chart.render();
                            },
                            complete: function () {
                                $('#loading').hide()
                            }
                        });
                    }

                    function earningStatisticsUpdate(t) {
                        let value = $(t).attr('data-earn-type');
                        $.ajax({
                            url: '{{route('admin.dashboard.earning-statistics')}}',
                            type: 'GET',
                            data: {
                                type: value
                            },
                            beforeSend: function () {
                                $('#loading').show()
                            },
                            success: function (response_data) {
                                document.getElementById("line-adwords").remove();
                                let graph = document.createElement('div');
                                graph.setAttribute("id", "line-adwords");
                                document.getElementById("updatingData").appendChild(graph);

                                var optionsLine = {
                                    chart: {
                                        height: 328,
                                        type: 'line',
                                        zoom: {
                                            enabled: false
                                        },
                                        toolbar: {
                                            show: false,
                                        },
                                    },
                                    stroke: {
                                        curve: 'straight',
                                        width: 2
                                    },
                                    colors: ['#87bcbf', '#107980'],
                                    series: [{
                                        name: "{{ translate('Earning') }}",
                                        data: response_data.earning,
                                    }],
                                    markers: {
                                        size: 6,
                                        strokeWidth: 0,
                                        hover: {
                                            size: 9
                                        }
                                    },
                                    grid: {
                                        show: true,
                                        padding: {
                                            bottom: 0
                                        },
                                        borderColor: "#d9e7ef",
                                        strokeDashArray: 7,
                                        xaxis: {
                                            lines: {
                                                show: true
                                            }
                                        }
                                    },
                                    labels: response_data.earning_label,
                                    xaxis: {
                                        tooltip: {
                                            enabled: false
                                        }
                                    },
                                    legend: {
                                        position: 'top',
                                        horizontalAlign: 'right',
                                        offsetY: -20
                                    }
                                }
                                var chartLine = new ApexCharts(document.querySelector('#line-adwords'), optionsLine);
                                chartLine.render();
                            },
                            complete: function () {
                                $('#loading').hide()
                            }
                        });
                    }
                </script>


        @endpush

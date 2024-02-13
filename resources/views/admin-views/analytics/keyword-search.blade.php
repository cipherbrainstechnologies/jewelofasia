@extends('layouts.admin.app')

@section('title', translate('Keyword_Search_Analytics'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/analytics_logo.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('Keyword_Search_Analytics')}}
                </span>
            </h1>
        </div>

        <div class="row gy-3">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <h4>{{translate('Trending_Keywords')}}</h4>
                            <div class="select-wrap d-flex flex-wrap gap-10">
                                <select class="form-control js-select2-custom trending-keywords__select" name="date_range">
                                    <option value="today" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='today'?'selected':''}}>{{translate('Today')}}</option>
                                    <option value="all_time" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='all_time'?'selected':''}}>{{translate('All_Time')}}</option>
                                    <option
                                        value="this_week" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_week'?'selected':''}}>{{translate('This_Week')}}</option>
                                    <option
                                        value="last_week" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='last_week'?'selected':''}}>{{translate('Last_Week')}}</option>
                                    <option
                                        value="this_month" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_month'?'selected':''}}>{{translate('This_Month')}}</option>
                                    <option
                                        value="last_month" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='last_month'?'selected':''}}>{{translate('Last_Month')}}</option>
                                    <option
                                        value="last_15_days" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='last_15_days'?'selected':''}}>{{translate('Last_15_Days')}}</option>
                                    <option
                                        value="this_year" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_year'?'selected':''}}>{{translate('This_Year')}}</option>
                                    <option
                                        value="last_year" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='last_year'?'selected':''}}>{{translate('Last_Year')}}</option>
                                    <option
                                        value="last_6_month" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='last_6_month'?'selected':''}}>{{translate('Last_6_Month')}}</option>
                                    <option
                                        value="this_year_1st_quarter" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_year_1st_quarter'?'selected':''}}>{{translate('This_Year_1st_Quarter')}}</option>
                                    <option
                                        value="this_year_2nd_quarter" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_year_2nd_quarter'?'selected':''}}>{{translate('This_Year_2nd_Quarter')}}</option>
                                    <option
                                        value="this_year_3rd_quarter" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_year_3rd_quarter'?'selected':''}}>{{translate('This_Year_3rd_Quarter')}}</option>
                                    <option
                                        value="this_year_4th_quarter" {{array_key_exists('date_range', $query_params) && $query_params['date_range']=='this_year_4th_quarter'?'selected':''}}>{{translate('this_year_4th_quarter')}}</option>
                                </select>
                            </div>
                        </div>
                        @if(count($graph_data['count']) < 1 && count($graph_data['keyword']) < 1)
                            <div class="text-center py-4">{{translate('No data available')}}</div>
                        @endif
                        <div id="apex_radial-bar-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <h4>{{translate('category_Wise_Search_Volume')}}</h4>
                            <div class="select-wrap d-flex flex-wrap gap-10">
                                <select class="form-control js-select2-custom w-100 zone-search-volume__select"
                                        id="date-range"
                                        name="date_range_2">
                                    <option value="today"  {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='today'?'selected':''}}>{{translate('Today')}}</option>

                                    <option
                                        value="all_time" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='all_time'?'selected':''}}>{{translate('All_Time')}}</option>
                                    <option
                                        value="this_week" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_week'?'selected':''}}>{{translate('This_Week')}}</option>
                                    <option
                                        value="last_week" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='last_week'?'selected':''}}>{{translate('Last_Week')}}</option>
                                    <option
                                        value="this_month" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_month'?'selected':''}}>{{translate('This_Month')}}</option>
                                    <option
                                        value="last_month" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='last_month'?'selected':''}}>{{translate('Last_Month')}}</option>
                                    <option
                                        value="last_15_days" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='last_15_days'?'selected':''}}>{{translate('Last_15_Days')}}</option>
                                    <option
                                        value="this_year" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_year'?'selected':''}}>{{translate('This_Year')}}</option>
                                    <option
                                        value="last_year" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='last_year'?'selected':''}}>{{translate('Last_Year')}}</option>
                                    <option
                                        value="last_6_month" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='last_6_month'?'selected':''}}>{{translate('Last_6_Month')}}</option>
                                    <option
                                        value="this_year_1st_quarter" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_year_1st_quarter'?'selected':''}}>{{translate('This_Year_1st_Quarter')}}</option>
                                    <option
                                        value="this_year_2nd_quarter" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_year_2nd_quarter'?'selected':''}}>{{translate('This_Year_2nd_Quarter')}}</option>
                                    <option
                                        value="this_year_3rd_quarter" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_year_3rd_quarter'?'selected':''}}>{{translate('This_Year_3rd_Quarter')}}</option>
                                    <option
                                        value="this_year_4th_quarter" {{array_key_exists('date_range_2', $query_params) && $query_params['date_range_2']=='this_year_4th_quarter'?'selected':''}}>{{translate('this_year_4th_quarter')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="row gy-3">
                                <div class="col-lg-5">
                                    <div
                                        class="bg-light h-100 rounded d-flex justify-content-center align-items-center p-3">
                                        <div class="text-center">
                                            <img class="mb-2" width="50"
                                                 src="{{asset('public/assets/admin/img/analytics_logo.png')}}"
                                                 alt="">
                                            <h2 class="mb-2">{{$searched_keyword_count}}</h2>
                                            <p>{{translate('Total Search Volume')}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="max-h320-auto">
                                        <ul class="common-list after-none gap-10 d-flex flex-column list-unstyled">
                                            @foreach($category_wise_volumes as $item)
                                                <li>
                                                    <div
                                                        class="mb-2 d-flex align-items-center justify-content-between gap-10 flex-wrap">
                                                        <span class="zone-name">{{$item->category?->name}}</span>
                                                        <span class="booking-count">{{number_format(($item['count']*100)/$total, 2)  }} %</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar"
                                                             style="width: {{ ($item['count']*100)/$total }}%"
                                                             aria-valuenow="25" aria-valuemin="0"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="card--header">
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
                                   class="form-control"
                                   placeholder="{{translate('search_by_Keyword')}}"
                                   aria-label="Search"
                                   value="{{$search??''}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <button type="button" class="btn btn-outline--primary text-nowrap btn-block"
                            data-toggle="dropdown">
                        <i class="tio-download-to"></i>
                        {{translate('Export')}}
                        <i class="tio-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.analytics.keyword.export.excel') }}">{{translate('Excel')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table
                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('Keyword')}}</th>
                        <th class="text-center">{{translate('Search Volume')}}</th>
                        <th class="text-center">{{translate('Related Categories')}}</th>
                        <th class="text-center">{{translate('Related Products')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($searched_table_data as $key=>$item)
                        <tr>
                            <td>{{$searched_table_data->firstitem()+$key}}</td>
                            <td>{{$item->keyword??''}}</td>
                            <td class="text-center">{{$item->volume_count ?? ''}}</td>
                            <td class="text-center">
                                <a href="#" data-toggle="tooltip" data-html="true" data-placement="right" title="
                                        <?php
                                            $categories = json_decode($item->searched_category);
                                            foreach ($categories as $cat) {
                                                echo $cat->category?->name . '<br>' ;
                                            }

                                        ?>">
                                    {{$item->searched_category_count}}</a>

                            </td>
                            <td class="text-center">{{$item->searched_product_count}}</td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <table>
                    <tfoot>
                    {!! $searched_table_data->links() !!}
                    </tfoot>
                    @if(count($searched_table_data) == 0)
                        <div class="text-center p-4">
                            <img class="w-120px mb-3"
                                 src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}"
                                 alt="Image Description">
                            <p class="mb-0">{{translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </table>

            </div>
            <!-- End Table -->
        </div>
    </div>

        @endsection

        @push('script_2')
            <script src="{{asset('/public/assets/admin/js/apex-charts/apexcharts.js')}}"></script>
            <script>
                var options = {
                    //series: @json($graph_data['count']),
                    series: @json($graph_data['avg']),
                    chart: {
                        height: 350,
                        type: 'radialBar',
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                margin: 10,
                                size: '55%',
                            },
                            dataLabels: {
                                name: {
                                    fontSize: '16px',
                                },
                                value: {
                                    fontSize: '14px',
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        // By default, this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                        return {{array_sum($graph_data['count'])}}
                                    }
                                }
                            }
                        }
                    },
                    labels: @json(count($graph_data['keyword']) > 0 ? $graph_data['keyword'] : ''),
                    colors: ['#286CD1', '#FFC700', '#A2CEEE', '#79CCA5', '#FFB16D'],
                    legend: {
                        show: true,
                        floating: false,
                        fontSize: '12px',
                        position: 'bottom',
                        horizontalAlign: 'center',
                        offsetY: -10,
                        itemMargin: {
                            horizontal: 5,
                            vertical: 10
                        },
                        labels: {
                            useSeriesColors: true,
                        },
                        markers: {
                            size: 0
                        },
                        formatter: function (seriesName, opts) {
                            return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
                        },
                    },
                };

                var chart = new ApexCharts(document.querySelector("#apex_radial-bar-chart"), options);
                chart.render();
            </script>

            <script>
                $(".trending-keywords__select").on('change', function () {
                    if (this.value !== "") location.href = "{{route('admin.analytics.keyword-search')}}" + '?date_range=' + this.value + '&date_range_2=' + '{{$query_params['date_range_2']??'all_time'}}';
                });
                $(".zone-search-volume__select").on('change', function () {
                    if (this.value !== "") location.href = "{{route('admin.analytics.keyword-search')}}" + '?date_range=' + '{{$query_params['date_range']??'all_time'}}' + '&date_range_2=' + this.value;
                });
            </script>
    @endpush

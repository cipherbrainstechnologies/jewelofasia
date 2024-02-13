<div class="card h-100 bg-white">
    <div class="card-body p-20px pb-0">
        <div class="btn--container justify-content-between align-items-center">
            <h5 class="card-title mb-2">
                <img src="{{asset('/public/assets/admin/img/order-statistics.png')}}" alt="" class="card-icon">
                <span>{{translate('order_statistics')}}</span>
            </h5>
            <div class="mb-2">
                <div class="d-flex flex-wrap statistics-btn-grp">
                    <label>
                        <input type="radio" name="order__statistics" hidden>
                        <span data-order-type="yearEarn"
                              onclick="orderStatisticsUpdate(this)">{{translate('This_Year')}}</span>
                    </label>
                    <label>
                        <input type="radio" name="order__statistics" hidden>
                        <span data-order-type="MonthEarn"
                              onclick="orderStatisticsUpdate(this)">{{translate('This_Month')}}</span>
                    </label>
                    <label>
                        <input type="radio" name="order__statistics" hidden>
                        <span data-order-type="WeekEarn"
                              onclick="orderStatisticsUpdate(this)">{{translate('This Week')}}</span>
                    </label>
                </div>
            </div>
        </div>
        <div id="">
            <canvas id="line-chart-1" class="initial--26"
              data-hs-chartjs-options='{
                series: [{
                    name: "Orders",
                    data: [
                        {{$order_statistics_chart[1]}}, {{$order_statistics_chart[2]}}, {{$order_statistics_chart[3]}}, {{$order_statistics_chart[4]}},
                        {{$order_statistics_chart[5]}}, {{$order_statistics_chart[6]}}, {{$order_statistics_chart[7]}}, {{$order_statistics_chart[8]}},
                        {{$order_statistics_chart[9]}}, {{$order_statistics_chart[10]}}, {{$order_statistics_chart[11]}}, {{$order_statistics_chart[12]}}
                    ],
                }],
                chart: {
                    height: 316,
                    type: "line",
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
                colors: ["#87bcbf", "#107980"],
                stroke: {
                    curve: "smooth",
                    width: 3,
                },
                categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
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

              }'>
            </canvas>

        </div>
    </div>
</div>


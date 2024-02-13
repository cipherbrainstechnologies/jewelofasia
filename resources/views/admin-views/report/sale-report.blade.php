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
                        <form action="javascript:" id="search-form" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-3">
                                    <label class="input-label">{{translate('Select Branch')}}</label>
                                    <select class="custom-select custom-select" name="branch_id" id="branch_id"
                                            required>
                                        <option selected disabled>{{translate('Select Branch')}}</option>
                                        <option value="all">{{translate('All')}}</option>
                                        @foreach(\App\Model\Branch::all() as $branch)
                                            <option
                                                value="{{$branch['id']}}" {{session('branch_filter')==$branch['id']?'selected':''}}>{{$branch['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6 col-md-3">
                                    <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                                    <label class="input-date">
                                        <input type="text" name="from" id="from_date" class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                                    </label>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                        <label class="input-label">{{translate('end')}} {{translate('date')}}</label>
                                        <label class="input-date">
                                        <input type="text" name="to" id="to_date" class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                                </label>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <label class="input-label d-none d-md-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">{{translate('show')}}</button>
                                </div>

                                <div class="col-md-12 pt-4">
                                    <div class="report--data">
                                        <div class="row g-3">
                                            <div class="col-sm-4">
                                                <div class="order--card h-100">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                            <span>{{translate('total orders')}}</span>
                                                        </h6>
                                                        <span class="card-title text-success" id="order_count"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="order--card h-100">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                            <span>{{translate('total item qty')}}</span>
                                                        </h6>
                                                        <span class="card-title text-success" id="item_count"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="order--card h-100">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                            <span>{{translate('total amount')}}</span>
                                                        </h6>
                                                        <span class="card-title text-success" id="order_amount"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive" id="set-rows">
                    @include('admin-views.report.partials._table',['data'=>[]])
                </div>
                <!-- End Table -->

            </div>
            <!-- End Row -->
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function () {
            $.post({
                url: "{{route('admin.report.sale-report-filter')}}",
                data: $('#search-form').serialize(),

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#order_count').html(data.order_count);
                    $('#order_amount').html(data.order_sum);
                    $('#item_count').html(data.item_qty);
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                    $('.report--data').slideDown(300)
                },
            });
        });

    </script>
    <script>
        /*$('#from_date,#to_date').change(function () {
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
        });*/

        $("#from_date").on("change", function () {
            console.log('aaa')
            $('#to_date').attr('min',$(this).val());
            $('#to_date').attr('required', true);
        });

        $("#to_date").on("change", function () {
            console.log('bbb')
            $('#from_date').attr('max',$(this).val());
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

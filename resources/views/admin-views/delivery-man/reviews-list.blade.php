@extends('layouts.admin.app')

@section('title', translate('Review List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/star.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('Review List')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header flex-between">
                <div class="card--header">
                    <h5 class="card-title">{{translate('Review list Table')}} <span class="badge badge-soft-secondary badge-pill">{{ $reviews->total() }}</span> </h5>
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
                                    class="form-control"
                                    placeholder="{{translate('Search')}}" aria-label="Search"
                                    value="{{$search}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    {{__('Search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('#')}}</th>
                        <th>{{translate('deliveryman')}}</th>
                        <th>{{translate('customer')}}</th>
                        <th>{{translate('review')}}</th>
                        <th>{{translate('rating')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$reviews->firstItem()+$key}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    @if($review->delivery_man)
                                        <a href="{{route('admin.delivery-man.preview',[$review['delivery_man_id']])}}">
                                            {{$review->delivery_man->f_name.' '.$review->delivery_man->l_name}}
                                        </a>
                                    @else
                                        <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                            {{\App\CentralLogics\translate('DeliveryMan unavailable')}}
                                        </span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if(isset($review->customer))
                                    <a href="{{route('admin.customer.view',[$review->user_id])}}">
                                        {{$review->customer->f_name." ".$review->customer->l_name}}
                                    </a>
                                @else
                                    <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                        {{\App\CentralLogics\translate('Customer unavailable')}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="max-200px line--limit-3">
                                    {{$review->comment}}
                                </div>
                            </td>
                            <td>
                                <label class="badge rating">
                                    {{$review->rating}} <i class="tio-star"></i>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <table>
                    <tfoot>
                    {!! $reviews->links() !!}
                    </tfoot>
                </table>
                @if(count($reviews)==0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                    </div>
                @endif
            </div>

            <!-- End Table -->
        </div>
        <!-- End Card -->
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush

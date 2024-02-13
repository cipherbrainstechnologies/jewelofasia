@extends('layouts.admin.app')

@section('title', translate('Delivery Man Preview'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/employee.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{$dm['f_name'].' '.$dm['f_name']}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

<!--        <div class="card">
            <div class="card-body pt-2">
                &lt;!&ndash; Nav &ndash;&gt;
                <ul class="nav nav-tabs page-header-tabs mb-4 Mt-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:">
                            {{translate('deliveryman')}} {{translate('reviews')}}
                        </a>
                    </li>
                </ul>
                &lt;!&ndash; End Nav &ndash;&gt;
                <div class="row g-3 justify-content-center">
                    &lt;!&ndash; Earnings (Monthly) Card Example &ndash;&gt;
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card bg&#45;&#45;2">
                            <h2 class="title">
                                560
                            </h2>
                            <h5 class="subtitle">
                                {{translate('total')}} {{translate('delivered')}} {{translate('orders')}}
                            </h5>
                            <img class="resturant-icon" src="{{asset('/public/assets/admin/img/tick.png')}}" alt="img">
                        </div>
                    </div>

                    &lt;!&ndash; Collected Cash Card Example &ndash;&gt;
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card bg&#45;&#45;3">
                            <h2 class="title">
                                650
                            </h2>
                            <h5 class="subtitle">
                                {{translate('cash_in_hand')}}
                            </h5>
                            <img class="resturant-icon" src="{{asset('/public/assets/admin/img/withdraw-amount.png')}}" alt="transactions">
                        </div>
                    </div>

                    &lt;!&ndash; Total Earning Card Example &ndash;&gt;
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card bg&#45;&#45;1">
                            <h2 class="title">
                                55
                            </h2>
                            <h5 class="subtitle">
                                {{translate('total_earning')}}
                            </h5>
                            <img class="resturant-icon" src="{{asset('/public/assets/admin/img/pending.png')}}" alt="transactions">
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
        <!-- Card -->
        <div class="card my-3">
<!--            <div class="card-header">
                <h5 class="card-title mr-auto">

                </h5>
                <div class="hs-unfold">
                    <a  href="javascript:" class="btn btn&#45;&#45;danger mr-2">
                            {{translate('Suspend Deliveryman')}}
                    </a>
                </div>
                <div class="hs-unfold">
                    <div class="dropdown">
                        <button class="btn btn&#45;&#45;reset initial-21 dropdown-toggle w-100" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            {{translate('type')}} ({{$dm->earning?translate('freelancer'):translate('salary_based')}})
                        </button>
                        <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item {{$dm->earning?'active':''}}"
                            onclick="request_alert('{{route('admin.dashboard',[$dm['id'],1])}}','{{translate('want_to_enable_earnings')}}')"
                                href="javascript:">{{translate('freelancer')}}</a>
                            <a class="dropdown-item {{$dm->earning?'':'active'}}"
                            onclick="request_alert('{{route('admin.dashboard',[$dm['id'],0])}}','{{translate('want_to_disable_earnings')}}')"
                                href="javascript:">{{translate('salary_based')}}</a>
                        </div>
                    </div>
                </div>
            </div>-->
            <!-- Body -->
            <div class="card-body">
                <div class="row align-items-md-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-center">
                            <img class="avatar avatar-xxl avatar-4by3 mr-4 mw-120px initial-22"
                                 onerror="this.src='{{asset('public/assets/admin/img/admin.png')}}'"
                                 src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}"
                                 alt="Image Description">
                            <div class="d-block">
                                <div class="rating--review">
                                    <h1 class="title">{{count($dm->rating)>0?number_format($dm->rating[0]->average, 1):0}}<span class="out-of">/5</span></h1>
                                    @if (count($dm->rating)>0)
                                    @if ($dm->rating[0]->average == 5)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 5 && $dm->rating[0]->average > 4.5)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-half"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 4.5 && $dm->rating[0]->average > 4)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 4 && $dm->rating[0]->average > 3)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 3 && $dm->rating[0]->average > 2)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 2 && $dm->rating[0]->average > 1)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average < 1 && $dm->rating[0]->average > 0)
                                    <div class="rating">
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average == 1)
                                    <div class="rating">
                                        <span><i class="tio-star"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @elseif ($dm->rating[0]->average == 0)
                                    <div class="rating">
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                        <span><i class="tio-star-outlined"></i></span>
                                    </div>
                                    @endif
                                    @endif
                                    <div class="info">
                                        {{-- <span class="mr-3">{{$dm->rating->count()}} {{translate('rating')}}</span> --}}
                                        <span>{{$dm->reviews->count()}} {{translate('reviews')}}</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <ul class="list-unstyled list-unstyled-py-2 mb-0 rating--review-right py-3">

                        @php($total=$dm->reviews->count())
                        <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],5))
                                <span class="progress-name mr-3">{{ translate('excellent') }}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($five/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$five}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($four=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],4))
                                <span class="progress-name mr-3">{{ translate('good') }}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($four/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$four}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($three=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],3))
                                <span class="progress-name mr-3">{{ translate('average') }}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($three/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$three}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($two=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],2))
                                <span class="progress-name mr-3">{{ translate('below_average') }}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($two/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$two}}</span>
                            </li>
                            <!-- End Review Ratings -->

                            <!-- Review Ratings -->
                            <li class="d-flex align-items-center font-size-sm">
                                @php($one=\App\CentralLogics\Helpers::dm_rating_count($dm['id'],1))
                                <span class="progress-name mr-3">{{ translate('poor') }}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($one/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$one}}</span>
                            </li>
                            <!-- End Review Ratings -->
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Body -->
        </div>
        <!-- End Card -->

        <!-- Card -->
        <div class="card border-top-0">
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table mb-0"
                       data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0, 3, 6],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('reviewer')}}</th>
                        <th>{{translate('review')}}</th>
                        <th>{{translate('date')}}</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                @if(isset($review->customer))
                                    <a class="d-flex align-items-center"
                                       href="{{route('admin.customer.view',[$review['user_id']])}}">
                                        <div class="avatar avatar-circle">
                                            <img class="avatar-img" width="75" height="75"
                                                 onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                                 src="{{asset('storage/app/public/profile/'.$review->customer->image)}}"
                                                 alt="Image Description">
                                        </div>
                                        <div class="ml-3">
                                        <span class="d-block h5 text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}} <i
                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                title="Verified Customer"></i></span>
                                            <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        </div>
                                    </a>
                                @else
                                    <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                                        {{\App\CentralLogics\translate('Customer unavailable')}}
                                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap w-18rem">
                                    <div class="d-flex">
                                        <span class="rating">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </span>
                                    </div>

                                    <p>
                                        {{$review['comment']}}
                                    </p>
                                </div>
                            </td>
                            <td>
                                {{date('d M Y H:i:s',strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer border-0">
                <!-- Pagination -->
                    {!! $reviews->links() !!}
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>
    function request_alert(url, message) {
        Swal.fire({
            title: '{{translate('are_you_sure')}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: '{{translate('no')}}',
            confirmButtonText: '{{translate('yes')}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>
@endpush

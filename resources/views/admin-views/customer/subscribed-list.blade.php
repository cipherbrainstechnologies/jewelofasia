@extends('layouts.admin.app')

@section('title', translate('Subscribed List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    {{translate('Subscribed Customers')}} <span class="badge badge-soft-primary ml-2 badge-pill">{{ $newsletters->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header flex-end">
                <div class="card--header">
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
                                   class="form-control"
                                   placeholder="{{translate('Ex : Search Emails Address')}}" aria-label="Search"
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
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       style="width: 100%">
                    <thead class="thead-light">
                    <tr>
                        <th class="">{{translate('#')}}</th>
                        <th>{{translate('email')}}</th>
                        <th>{{translate('subscribed_at')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($newsletters as $key=>$newsletter)
                        <tr class="">
                            <td class="">
                                {{$newsletters->firstitem()+$key}}
                            </td>
                            <td>
                                <a href="mailto:{{$newsletter['email']}}?subject={{translate('Mail from '). \App\Model\BusinessSetting::where(['key' => 'restaurant_name'])->first()->value}}">{{$newsletter['email']}}</a>
                            </td>
                            <td>{{date('Y/m/d '.config('timeformat'), strtotime($newsletter->created_at))}}</td>
                        </tr>

                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <div class="row">
                    <div class="col-12" style="overflow-x: scroll;">
                        {!! $newsletters->links() !!}
                    </div>
                </div>
            </div>
            @if(count($newsletters) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
            @endif
            <!-- End Footer -->
        </div>
        <!-- End Card -->

        <div class="modal fade" id="add-point-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" id="modal-content"></div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush

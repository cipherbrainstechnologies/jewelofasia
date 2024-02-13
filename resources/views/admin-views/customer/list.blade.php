@extends('layouts.admin.app')

@section('title', translate('Customer List'))

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
                    {{translate('customers list')}} <span class="badge badge-soft-primary ml-2 badge-pill">{{\App\User::count()}}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="card--header">
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
                                class="form-control"
                                placeholder="{{translate('Search by Name or Phone or Email')}}" aria-label="Search"
                                value="{{$search}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    {{__('Search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold ml-auto">
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
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.customer.export', ['search'=>Request::get('search')])}}">
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
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-hover table-align-middle m-0 text-14px">
                    <thead class="thead-light">
                    <tr class="word-nobreak">
                        <th class="">
                            {{translate('#')}}
                        </th>
                        <th class="table-column-pl-0">{{translate('customer name')}}</th>
                        <th>{{translate('contact info')}}</th>
                        <th class="text-center">{{translate('Total Orders')}}</th>
                        <th class="text-center">{{translate('Total Order Amount')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($customers as $key=>$customer)
                        <tr>
                            <td>
                                {{$customers->firstItem()+$key}}
                            </td>
                            <td class="table-column-pl-0">
                                <a href="{{route('admin.customer.view',[$customer['id']])}}" class="product-list-media">
                                    <img class="rounded-full"
                                         src="{{asset('storage/app/public/profile/'.$customer->image)}}"
                                        onerror="this.src='{{asset('/public/assets/admin/img/admin.png')}}'">
                                    <div class="table--media-body">
                                        <h5 class="title m-0">
                                            {{$customer['f_name']." ".$customer['l_name']}}
                                        </h5>
                                    </div>
                                </a>
                            </td>
                            <td>
                                <h5 class="m-0">
                                    <a href="mailto:{{$customer['email']}}">{{$customer['email']}}</a>
                                </h5>
                                <div>
                                    <a href="Tel:{{$customer['phone']}}">{{$customer['phone']}}</a>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <a href="{{route('admin.customer.view',[$customer['id']])}}">
                                        <span class="badge badge-soft-info py-2 px-3 font-medium">
                                            {{$customer->orders->count()}}
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    {{ Helpers::set_symbol(\App\User::total_order_amount($customer->id)) }}
                                </div>
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                           onclick="status_change_alert('{{ route('admin.customer.status', [$customer->id, $customer->is_block ? 0 : 1]) }}', '{{ $customer->is_block? translate('you_want_to_change_the_status_for_this_customer'): translate('you_want_to_change_the_status_for_this_customer') }}', event)"
                                           class="toggle-switch-input" id="stocksCheckbox{{ $customer->id }}"
                                        {{ $customer->is_block ? '' : 'checked' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="{{route('admin.customer.view',[$customer['id']])}}">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                       onclick="form_alert('customer-{{$customer['id']}}','{{translate('Want to remove this customer ?')}}')">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.customer.delete',[$customer['id']])}}"
                                          method="post" id="customer-{{$customer['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($customers) == 0)
            <div class="text-center p-4">
                <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                <p class="mb-0">{{translate('No_data_to_show')}}</p>
            </div>
            @endif
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                {!! $customers->links() !!}
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.customer.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
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
@endpush

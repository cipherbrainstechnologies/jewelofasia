@extends('layouts.admin.app')

@section('title', translate('Product List'))

@section('content')
    <div class="content container-fluid product-list-page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/products.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{ translate('product List') }}
                    <span class="badge badge-soft-secondary">{{ $products->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header border-0">
                        <div class="card--header justify-content-end max--sm-grow">
                            <form action="{{url()->current()}}" method="GET" class="mr-sm-auto">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search_by_ID_or_name')}}" aria-label="Search"
                                        value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{translate('search')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
<!--                                    <span class="dropdown-header">{{ translate('options') }}</span>
                                    <a id="export-copy" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/copy.svg"
                                            alt="Image Description">
                                        {{ translate('copy') }}
                                    </a>
                                    <a id="export-print" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/print.svg"
                                            alt="Image Description">
                                        {{ translate('print') }}
                                    </a>
                                    <div class="dropdown-divider"></div>-->
                                    <span class="dropdown-header">{{ translate('download') }}
                                        {{ translate('options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{route('admin.product.bulk-export')}}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('excel') }}
                                    </a>
<!--                                    <a id="export-csv" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('csv') }}
                                    </a>
                                    <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/pdf.svg"
                                            alt="Image Description">
                                        {{ translate('pdf') }}-->
<!--                                    </a>-->
                                </div>
                            </div>
                            <!-- End Unfold -->
                            <div>
                                <a href="{{route('admin.product.limited-stock')}}" class="btn btn--primary-2 min-height-40">{{translate('limited stocks')}}</a>
                            </div>
                            <div>
                                <a href="{{route('admin.product.add-new')}}" class="btn btn-primary min-height-40 py-2"><i
                                        class="tio-add"></i>
                                    {{translate('add new product')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th>{{translate('product_name')}}</th>
                                <th>{{translate('selling_price')}}</th>
                                <th class="text-center">{{translate('total_sale')}}</th>
                                <th class="text-center">{{translate('show_in_daily_needs')}}</th>
                                <th class="text-center">{{translate('featured')}}</th>
                                <th class="text-center">{{translate('status')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($products as $key=>$product)
                                <tr>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">{{$products->firstItem()+$key}}</td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <a href="{{route('admin.product.view',[$product['id']])}}" class="product-list-media">
                                            @if (!empty(json_decode($product['image'],true)))
                                        <img
                                            src="{{asset('storage/app/public/product')}}/{{json_decode($product['image'],true)[0]}}"
                                            onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'">
                                        @else
                                            <img src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}">
                                        @endif
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($product['name'], 20, $end='...')}}
                                        </h6>
                                        </a>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <div class="max-85 text-right">
                                            {{ Helpers::set_symbol($product['price']) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $product->total_sold }}
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <div class="text-center">
                                            <label class="switch my-0">
                                                <input type="checkbox" class="status" onchange="daily_needs('{{$product['id']}}','{{$product->daily_needs==1?0:1}}')"
                                                    id="{{$product['id']}}" {{$product->daily_needs == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <label class="toggle-switch my-0">
                                            <input type="checkbox"
                                                   onclick="featured_status_change_alert('{{ route('admin.product.feature', [$product->id, $product->is_featured ? 0 : 1]) }}', '{{ $product->is_featured? translate('want to remove from featured product'): translate('want to add in featured product') }}', event)"
                                                   class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                                {{ $product->is_featured ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <label class="toggle-switch my-0">
                                            <input type="checkbox"
                                                onclick="status_change_alert('{{ route('admin.product.status', [$product->id, $product->status ? 0 : 1]) }}', '{{ $product->status? translate('you want to disable this product'): translate('you want to active this product') }}', event)"
                                                class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}"
                                                {{ $product->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label mx-auto text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                        <!-- Dropdown -->
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.product.edit',[$product['id']])}}">
                                            <i class="tio-edit"></i></a>
                                            <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('product-{{$product['id']}}','{{ translate("Want to delete this") }}')">
                                                <i class="tio-delete-outlined"></i>
                                            </a>
                                        </div>
                                        <form action="{{route('admin.product.delete',[$product['id']])}}"
                                                method="post" id="product-{{$product['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="page-area">
                            <table>
                                <tfoot class="border-top">
                                {!! $products->links() !!}
                                </tfoot>
                            </table>
                        </div>
                        @if(count($products)==0)
                            <div class="text-center p-4">
                                <img class="w-120px mb-3" src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                                <p class="mb-0">{{translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate("Are you sure?") }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: '{{ translate("No") }}',
                confirmButtonText: '{{ translate("Yes") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>

<script>
    function featured_status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>
    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>

    <script>
        function daily_needs(id, status) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.daily-needs')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{ translate("Daily need status updated successfully") }}');
                }
            });
        }
    </script>
@endpush

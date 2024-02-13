@extends('layouts.admin.app')

@section('title', translate('flash_sale_product'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/flash_sale.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('flash deal product')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 text-capitalize">{{$flash_deal['title']}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.offer.flash.add-product',[$flash_deal['id']])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name" class="title-color text-capitalize">{{ translate('Add new product')}}</label>
                                        <select class="js-example-basic-multiple js-states js-example-responsive form-control h--45px" name="product_id">
                                            <option disabled selected>{{ translate('Select Product')}}</option>
                                            @foreach ($products as $key => $product)
                                                <option value="{{ $product->id }}">
                                                    {{$product['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="btn--container justify-content-end">
                                    <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <h5 class="mb-0 text-capitalize">
                            {{ translate('Product')}} {{ translate('Table')}}
                            <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $flash_deal_products->total() }}</span>
                        </h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100" cellspacing="0">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{translate('name')}}</th>
                                <th>{{ translate('actual_price')}}</th>
                                <th>{{ translate('discount')}}</th>
                                <th>{{ translate('discount_price')}}</th>
                                <th class="text-center">{{ translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($flash_deal_products as $k=>$de_p)
                                @php($discount = Helpers::discount_calculate($de_p, $de_p['price']))
                                <tr>
                                    <td>{{$flash_deal_products->firstitem()+$k}}</td>
                                    <td><a href="{{ route('admin.product.view', [$de_p['id']]) }}" target="_blank" class="font-weight-semibold title-color hover-c1">{{$de_p['name']}}</a></td>
                                    <td>{{ Helpers::set_symbol($de_p['price']) }}</td>
                                    <td>{{ Helpers::set_symbol($discount) }}</td>
                                    <td>{{ Helpers::set_symbol($de_p['price'] - $discount) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a  title="{{ trans ('Delete')}}"
                                                class="btn btn-outline-danger btn-sm delete"
                                                id="{{$de_p['id']}}">
                                                <i class="tio-delete"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $flash_deal_products->links() !!}
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')

    <script>

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            var flash_deal_id = {{ $flash_deal->id }}
            Swal.fire({
                title: "{{translate('Are_you_sure_remove_this_product')}}?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate('Yes')}}, {{translate('delete_it')}}!',
                type: 'warning',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.offer.flash.delete.product')}}",
                        method: 'POST',
                        data: {
                                id: id,
                                flash_deal_id : flash_deal_id
                            },
                        success: function (data) {
                            toastr.success('{{translate('product_removed_successfully')}}');
                            location.reload();
                        },
                    });
                }
            })
        });
    </script>

@endpush

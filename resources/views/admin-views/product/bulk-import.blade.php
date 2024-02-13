@extends('layouts.admin.app')

@section('title', translate('Product Bulk Import'))

@push('css_or_js')

@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/bulk.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{ translate('bulk') }} {{ translate('import') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <!-- Content Row -->
        <div class="row g-2">
            <div class="col-12">
                <div class="jumbotron mb-0 p-3 p-md-4 shadow bg-white">
                    <h2 class="title">{{ translate('Instructions') }} : </h2>
                    <p> {{ translate('1. Download the format file and fill it with proper data.') }}</p>

                    <p>{{ translate('2. You can download the example file to understand how the data must be filled.') }}</p>

                    <p>{{ translate('3. Once you have downloaded and filled the format file, upload it in the form below and submit.') }}</p>

                    <p> {{ translate("4. After uploading products you need to edit them and set product's images and choices.") }}</p>

                    <p class="mb-0"> {{ translate('5. You can get category and sub-category id from their list, please input the right ids.') }}</p>

                </div>
            </div>

            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.product.bulk-import')}}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-center mt-2">
                                {{ translate("Do not have the template") }}?
                                <a href="{{asset('public/assets/product_bulk_format.xlsx')}}" download="" class="text--primary-2">{{ translate('Download Here') }}</a>
                            </h5>
                            <div class="form-group mt-4">
                                <label class="drag--area mb-0 mx-auto cursor-pointer" ondrop="drop(event)" ondragover="allowDrop(event)">
                                    <img class="icon" src="{{asset('/public/assets/admin/img/cloud.png')}}" alt="">
                                    <div class="drag-header">{{ translate('Click here to import the .xlsx file here') }}</div>
                                    <div class="__choose-input">
                                        <input type="file" name="products_file" class="form-control" id="import-file" accept=".xlsx" >
                                    </div>
                                    <div class="file--img"></div>
                                </label>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('Submit') }}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection


@push('script_2')
<script>
    $('#import-file').on('change', function(){
        if($(this)[0].files.length !== 0){
        $('.file--img').empty().append(`<div class="my-2"> <img src="{{asset('/public/assets/admin/img/excel.png')}}" alt=""></div>`)
        }
    })
    $('.product-form').on('reset', function(){
        $('.file--img').empty()
    })

</script>
@endpush

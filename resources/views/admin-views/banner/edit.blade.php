@extends('layouts.admin.app')

@section('title', translate('Update banner'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('Update Banner')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.banner.update',[$banner['id']])}}" method="post"
                      enctype="multipart/form-data">
                    @csrf @method('put')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                        <input type="text" name="title" value="{{$banner['title']}}" class="form-control"
                                            placeholder="{{ translate('New banner') }}" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('item')}} {{translate('type')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="item_type" class="form-control" onchange="show_item(this.value)">
                                            <option value="product" {{$banner['product_id']==null?'':'selected'}}>{{translate('product')}}</option>
                                            <option value="category" {{$banner['category_id']==null?'':'selected'}}>{{translate('category')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0" id="type-product"
                                        style="display: {{$banner['product_id']==null?'none':'block'}}">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('product')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            @foreach($products as $product)
                                                <option
                                                    value="{{$product['id']}}" {{$banner['product_id']==$product['id']?'selected':''}}>
                                                    {{$product['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="type-category"
                                        style="display: {{$banner['category_id']==null?'none':'block'}}">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="form-control js-select2-custom">
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}" {{$banner['category_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <div class="text-center mb-3">
                                    {{translate('banner')}} {{translate('image')}}
                                    <small class="text-danger">* ( {{translate('ratio')}} 2:1 )</small>
                                </div>
                                <label class="upload--vertical">
                                    <input type="file" name="image" id="customFileEg1" class=""
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                                    <img id="viewer" onerror="this.src='{{asset('public/assets/admin/img/upload-vertical.png')}}'" src="{{asset('storage/app/public/banner')}}/{{$banner['image']}}" alt="banner image"/>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    </script>


@endpush

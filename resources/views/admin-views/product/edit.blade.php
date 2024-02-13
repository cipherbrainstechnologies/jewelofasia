@extends('layouts.admin.app')

@section('title', translate('Update product'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{translate('product')}} {{translate('update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="javascript:" method="post" id="product_form"
                enctype="multipart/form-data"class="row g-2">
            @csrf
            @php($data = Helpers::get_business_settings('language'))
            @php($default_lang = Helpers::get_default_language())

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body pt-2">
                        @if($data && array_key_exists('code', $data[0]))
                            <ul class="nav nav-tabs mb-4">

                                @foreach($data as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang['code'] == 'en'? 'active':''}}" href="#" id="{{$lang['code']}}-link">{{Helpers::get_language_name($lang['code']).'('.strtoupper($lang['code']).')'}}</a>
                                </li>
                                @endforeach

                            </ul>
                            @foreach($data as $lang)
                                <?php
                                    if(count($product['translations'])){
                                        $translate = [];
                                        foreach($product['translations'] as $t)
                                        {
                                            if($t->locale == $lang['code'] && $t->key=="name"){
                                                $translate[$lang['code']]['name'] = $t->value;
                                            }
                                            if($t->locale == $lang['code'] && $t->key=="description"){
                                                $translate[$lang['code']]['description'] = $t->value;
                                            }

                                        }
                                    }
                                ?>
                                <div class="{{$lang['code'] != 'en'? 'd-none':''}} lang_form" id="{{$lang['code']}}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{$lang['code']}}_name">{{translate('name')}} ({{strtoupper($lang['code'])}})</label>
                                        <input type="text" {{$lang['status'] == true ? 'required':''}} name="name[]" id="{{$lang['code']}}_name" value="{{$translate[$lang['code']]['name']??$product['name']}}" class="form-control" placeholder="{{translate('New Product')}}" >
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang['code']}}">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="{{$lang['code']}}_description">{{translate('short')}} {{translate('description')}}  ({{strtoupper($lang['code'])}})</label>
                                        <textarea name="description[]" class="form-control h--172px" id="{{$lang['code']}}_hiddenArea">{{$translate[$lang['code']]['description']??$product['description']}}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="english-form">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}} (EN)</label>
                                    <input type="text" name="name[]" value="{{$product['name']}}" class="form-control" placeholder="{{translate('New Product')}}" required>
                                </div>
                                <input type="hidden" name="lang[]" value="en">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('short')}} {{translate('description')}} (EN)</label>
                                    <textarea name="description[]" class="form-control h--172px" id="hiddenArea">{{ $product['description'] }}</textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                {{translate('category')}}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label"
                                            for="exampleFormControlSelect1">{{translate('category')}}<span
                                            class="input-label-secondary">*</span></label>
                                    <select name="category_id" id="category-id" class="form-control js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                        @foreach($categories as $category)
                                            <option
                                                value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label"
                                            for="exampleFormControlSelect1">{{translate('sub_category')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="sub_category_id" id="sub-categories"
                                            data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                            class="form-control js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label"
                                            for="exampleFormControlInput1">{{translate('unit')}}</label>
                                    <select name="unit" class="form-control js-select2-custom">
                                        <option value="kg" {{$product['unit']=='kg'?'selected':''}}>{{translate('kg')}}</option>
                                        <option value="gm" {{$product['unit']=='gm'?'selected':''}}>{{translate('gm')}}</option>
                                        <option value="ltr" {{$product['unit']=='ltr'?'selected':''}}>{{translate('ltr')}}</option>
                                        <option value="pc" {{$product['unit']=='pc'?'selected':''}}>{{translate('pc')}}</option>
                                        <option value="ml" {{$product['unit']=='ml'?'selected':''}}>{{translate('ml')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label"
                                            for="exampleFormControlInput1">{{translate('capacity')}}</label>
                                    <input type="number" min="0" step="0.01" value="{{$product['capacity']}}"  name="capacity"
                                            class="form-control"
                                            placeholder="{{ translate('Ex : 5') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('maximum_order_quantity')}}</label>
                                    <input type="number" min="1" step="1" value="{{$product['maximum_order_quantity']}}" name="maximum_order_quantity"
                                           class="form-control"
                                           placeholder="{{ translate('Ex : 3') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="card min-h-116px">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="d-flex flex-wrap-reverse justify-content-between">
                                <div class="w-200 flex-grow-1 mr-3">
                                    {{translate('Turning Visibility off will not show this product in the user app and website')}}
                                </div>
                                <div class="d-flex align-items-center mb-2 mb-sm-0">
                                    <h5 class="mb-0 mr-2">{{ translate('Visibility') }}</h5>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" class="toggle-switch-input" name="status" value="1" {{$product['status']==1?'checked':''}}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                    <h5 class="mb-3">{{translate('product')}} {{translate('image')}} <small
                        class="text-danger">* ( {{translate('ratio')}} 1:1 )</small></h5>
                        <div class="product--coba">
                            <div class="row g-2" id="coba">
                                @if (!empty(json_decode($product['image'],true)))
                                    @foreach(json_decode($product['image'],true) as $img)
                                        <div class="spartan_item_wrapper position-relative">
                                            <img class="img-150 border rounded p-3" src="{{asset('storage/app/public/product')}}/{{$img}}">
                                            <a href="{{route('admin.product.remove-image',[$product['id'],$img])}}" class="spartan__close"><i class="tio-add-to-trash"></i></a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-label"></i>
                            </span>
                            <span>
                                {{translate('tags')}}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="tags" placeholder="Enter tags" value="@foreach($product->tags as $c) {{$c->tag.','}} @endforeach" data-role="tagsinput">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-dollar"></i>
                            </span>
                            <span>
                                {{translate('price_information')}}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-2">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('default_unit_price')}}</label>
                                        <input type="number" value="{{$product['price']}}" min="0" max="100000000" name="price" class="form-control" step="any" placeholder="{{ translate('Ex : 100') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                                for="exampleFormControlInput1">{{translate('stock')}}</label>
                                        <input type="number" min="0" max="100000000" value="{{$product['total_stock']}}" name="total_stock" class="form-control"
                                                placeholder="{{ translate('Ex : 100') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                                for="exampleFormControlInput1">{{translate('discount')}} {{translate('type')}}</label>
                                        <select name="discount_type" id="discount_type" class="form-control js-select2-custom">
                                            <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>
                                                {{translate('percent')}}
                                            </option>
                                            <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>
                                                {{translate('amount')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                                for="exampleFormControlInput1">{{translate('discount')}} <span id="discount_symbol">{{$product['discount_type']=='amount'?'':'(%)'}}</span></label>
                                        <input type="number" min="0" value="{{$product['discount']}}" max="100000"
                                                name="discount" class="form-control" step="any"
                                                placeholder="{{ translate('Ex : 100') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                                for="exampleFormControlInput1">{{translate('tax_type')}}</label>
                                        <select name="tax_type" id="tax_type" class="form-control js-select2-custom">
                                            <option
                                                value="percent" {{$product['tax_type']=='percent'?'selected':''}}>{{translate('percent')}}
                                            </option>
                                            <option
                                                value="amount" {{$product['tax_type']=='amount'?'selected':''}}>{{translate('amount')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('tax_rate')}} <span id="tax_symbol">{{$product['tax_type']=='amount'?'':'(%)'}}</span></label>
                                        <input type="number" value="{{$product['tax']}}" min="0" max="100000" name="tax" class="form-control" step="any" placeholder="{{ translate('Ex : 7') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-puzzle"></i>
                            </span>
                            <span>
                                {{translate('attribute')}}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-2">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group __select-attr">
                                        <label class="input-label"
                                                for="exampleFormControlSelect1">{{translate('select attribute')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="attribute_id[]" id="choice_attributes"
                                                class="form-control js-select2-custom"
                                                multiple="multiple">
                                            @foreach(\App\Model\Attribute::orderBy('name')->get() as $attribute)
                                                <option
                                                    value="{{$attribute['id']}}" {{in_array($attribute->id,json_decode($product['attributes'],true))?'selected':''}}>{{$attribute['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="customer_choice_options" id="customer_choice_options">
                                        @include('admin-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                                    </div>
                                </div>
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="variant_combination" id="variant_combination">
                                        @include('admin-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'],true)])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="btn--container justify-content-end">
                    <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')
    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script>
        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == 'en')
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
                $("#from_part_2").addClass('d-none');
            }


        })
    </script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: '150px',
                groupClassName: '',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('/public/assets/admin/img/upload-en.png')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>

    <script>
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                let sub_sub_category = '{{count($product_category)>=3?$product_category[2]->id:''}}';
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
    </script>

    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.variant-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }
    </script>

    {{-- <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> --}}

    <script>


        $('#product_form').on('submit', function () {



            var formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',[$product['id']])}}',
                // data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate('product updated successfully!')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.product.list')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>

    <script>

        $('#discount_type').change(function(){
            if($('#discount_type').val() == 'percent') {
                $("#discount_symbol").html('(%)')
            } else {
                $("#discount_symbol").html('')
            }
        });

        $('#tax_type').change(function(){
            if($('#tax_type').val() == 'percent') {
                $("#tax_symbol").html('(%)')
            } else {
                $("#tax_symbol").html('')
            }
        });

    </script>

    <script>
        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for(var i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {
                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
                console.log(total_qty)
            }
            else{
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>

@endpush



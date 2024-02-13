@extends('layouts.admin.app')

@section('title', translate('Update Coupon'))

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
                    {{translate('coupon')}} {{translate('update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body p-xl-30">
                        <form action="{{route('admin.coupon.update',[$coupon['id']])}}" method="post">
                            @csrf
                            <div class="row gx--3">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('coupon')}} {{translate('type')}}</label>
                                        <select name="coupon_type" class="form-control" onchange="coupon_type_change(this.value)">
                                            <option value="default" {{$coupon['coupon_type']=='default'?'selected':''}}>{{translate('default')}}</option>
                                            <option value="first_order" {{$coupon['coupon_type']=='first_order'?'selected':''}}>{{translate('first')}} {{translate('order')}}</option>
                                            <option value="free_delivery" {{$coupon['coupon_type']=='free_delivery'?'selected':''}}>{{translate('free_delivery')}}</option>
                                            <option value="customer_wise" {{$coupon['coupon_type']=='customer_wise'?'selected':''}}>{{translate('customer_wise')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('coupon title')}}</label>
                                        <input type="text" name="title" value="{{$coupon['title']}}" class="form-control"
                                            placeholder="New coupon" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('coupon code')}}</label>
                                        <input type="text" name="code" class="form-control" value="{{$coupon['code']}}"
                                            placeholder="{{\Illuminate\Support\Str::random(8)}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6" id="limit-for-user" style="display: {{$coupon['coupon_type']=='first_order'?'none':'block'}}">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                        <input type="number" name="limit" id="user-limit" min="1" value="{{$coupon['limit']}}" class="form-control" {{ $coupon['coupon_type']!='first_order' ? 'required' : '' }}
                                            placeholder="{{ translate('EX: 10') }}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6" id="discount_type_div" style="display: {{$coupon['coupon_type']=='free_delivery'?'none':'block'}}">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}} {{translate('type')}}</label>
                                        <select name="discount_type" id="discount_type" class="form-control">
                                            <option value="amount" {{$coupon['discount_type']=='amount'?'selected':''}}>{{translate('amount')}}</option>
                                            <option value="percent" {{$coupon['discount_type']=='percent'?'selected':''}}>{{translate('percent')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6" id="discount_amount_div" style="display: {{$coupon['coupon_type']=='free_delivery'?'none':'block'}}">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}}</label>
                                        <input type="number" min="0" max="10000" step="any" value="{{$coupon['discount']}}"
                                            name="discount" class="form-control" >
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('min')}} {{translate('purchase')}}</label>
                                        <input type="number" name="min_purchase" step="any" value="{{$coupon['min_purchase']}}"
                                            min="0" max="100000" class="form-control"
                                            placeholder="{{ translate('100') }}">
                                    </div>
                                </div>
{{--                                style="@if($coupon['discount_type']=='amount') display: none; @endif"--}}
                                <div class="col-md-4 col-sm-6 {{$coupon['coupon_type']=='free_delivery' || $coupon->discount_type == 'amount'?'d-none':'d-block'}}" id="max_discount_div" >
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('max')}} {{translate('discount')}}</label>
                                        <input type="number" min="0" max="1000000" step="any"
                                            value="{{$coupon['max_discount']??0}}" name="max_discount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="">{{translate('start')}} {{translate('date')}}</label>
                                        <label class="input-date">
                                            <input type="text" name="start_date" id="start_date"
                                                   class="js-flatpickr form-control flatpickr-custom"
                                                   placeholder="{{ translate('Select dates') }}"
                                                   value="{{date('Y/m/d',strtotime($coupon['start_date']))}}"
                                                   data-hs-flatpickr-options='{
                                                "dateFormat": "Y/m/d", "minDate": "today"
                                            }'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="">{{translate('expire')}} {{translate('date')}}</label>
                                        <label class="input-date">
                                            <input type="text" name="expire_date" id="expire_date"
                                                   class="js-flatpickr form-control flatpickr-custom"
                                                   placeholder="{{ translate('Select dates') }}"
                                                   value="{{date('Y/m/d',strtotime($coupon['expire_date']))}}"
                                                   data-hs-flatpickr-options='{
                                                "dateFormat": "Y/m/d", "minDate": "today"
                                            }'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6" id="customer_div" style="display: {{$coupon['coupon_type']=='customer_wise'?'block':'none'}}">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('customer')}}</label>
                                        <select name="customer_id" id="customer_id" class="form-control js-select2-custom">
{{--                                            <option value="">{{translate('')}}</option>--}}
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}" {{ $customer->id == $coupon['customer_id'] ? 'selected' : '' }}>{{$customer->f_name.' '. $customer->l_name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>



        // $(".js-select2-custom").select2({
        //     placeholder: "select customer",
        //     allowClear: true
        // });
        //
        // $('form').on('reset', function(e)
        // {
        //     $(".js-select2-custom").val('').trigger('change')
        // });


        var initialSelect2Value = $('.js-select2-custom').val();
        $('form').on('reset', function() {
            this.reset();
            $('.js-select2-custom').val(initialSelect2Value).trigger('change');
        });




        $("#discount_type").change(function(){
            if(this.value === 'amount') {
                $("#max_discount_div").hide();
            }
            else if(this.value === 'percent') {
                $("#max_discount_div").show();
            }
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

        $('#start_date,#expire_date').change(function () {
            let fr = $('#start_date').val();
            let to = $('#expire_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#start_date').val('');
                    $('#expire_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });

        // function coupon_type_change(order_type) {
        //     if(order_type==='first_order'){
        //         $('#user-limit').removeAttr('required');
        //         $('#limit-for-user').hide();
        //     }else{
        //         $('#user-limit').prop('required',true);
        //         $('#limit-for-user').show();
        //     }
        // }

        function coupon_type_change(order_type) {
            if(order_type==='first_order'){
                $('#user-limit').removeAttr('required');
                $('#limit-for-user').hide();

                $('#customer_id').removeAttr('required',true);
                $('#customer_div').addClass('d-none');

                $('#discount_type_div').show();

                $('#discount_amount_div').show();

               // $('#max_discount_div').show();
            }
            else if(order_type==='customer_wise'){
                $('#user-limit').prop('required');
                $('#limit-for-user').show();

                $('#customer_id').prop('required', true);
                $('#customer_div').removeClass('d-none');

                $('#discount_type_div').show();

                $('#discount_amount_div').show();

                //$('#max_discount_div').show();
            }
            else if(order_type==='free_delivery'){
                $('#user-limit').prop('required');
                $('#limit-for-user').show();

                $('#customer_id').removeAttr('required',true);
                $('#customer_div').addClass('d-none');

                $('#discount_type_div').hide();

                $('#discount_amount_div').hide();

                //$('#max_discount_div').hide();
            }
            else{
                $('#user-limit').prop('required',true);
                $('#limit-for-user').show();

                $('#customer_id').removeAttr('required',true);
                $('#customer_div').addClass('d-none');

                $('#discount_type_div').show();

                $('#discount_amount_div').show();

                //$('#max_discount_div').show();
            }
        }
    </script>

@endpush

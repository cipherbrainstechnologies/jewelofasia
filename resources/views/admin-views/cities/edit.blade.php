@extends('layouts.admin.app')

@section('title', translate('Update Category Discount'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/coupon.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('discount update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('admin.discount.update', [$discount['id']])}}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                                <input type="text" name="name" value="{{$discount->name}}" class="form-control" placeholder="{{ translate('New discount') }}" maxlength="255" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0" id="type-category">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}} <span
                                        class="input-label-secondary">*</span></label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                        <option value="{{$category['id']}}" {{ $category['id'] == $discount['category_id'] ? 'selected' : '' }}>{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('start')}} {{translate('date')}}</label>
                                <label class="input-date">
                                    <input type="text" name="start_date" value="{{date('Y/m/d',strtotime($discount['start_date']))}}"
                                           id="start_date"
                                           class="js-flatpickr form-control flatpickr-custom"
                                           placeholder="{{ \App\CentralLogics\translate('dd/mm/yy') }}"
                                           data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }' required>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('expire')}} {{translate('date')}}</label>
                                <label class="input-date">
                                    <input type="text" name="expire_date" value="{{date('Y/m/d',strtotime($discount['expire_date']))}}"
                                           id="expire_date"
                                           class="js-flatpickr form-control flatpickr-custom"
                                           placeholder="{{ \App\CentralLogics\translate('dd/mm/yy') }}"
                                           data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }' required>
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('discount')}} {{translate('type')}}<span
                                        class="input-label-secondary">*</span></label>
                                <select name="discount_type" class="form-control" onchange="show_item(this.value)" id="discount_type">
                                    <option value="percent" {{ $discount['discount_type'] == 'percent'? 'selected' : '' }}>{{translate('percent')}}</option>
                                    <option value="amount" {{ $discount['discount_type'] == 'amount'? 'selected' : '' }}>{{translate('amount')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('discount_amount')}}</label>
                                <input type="number" step="0.1" name="discount_amount" value="{{$discount['discount_amount']}}" class="form-control" placeholder="{{ translate('discount_amount') }}" required>
                            </div>
                        </div>
                        <div class="col-6" id="max_amount_div">
{{--                        <div class="col-6 {{ $discount['discount_type'] == 'amount' ? 'd-none' : '' }}" id="max_amount_div">--}}
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('maximum_amount')}}</label>
                                <input type="number" step="0.1" name="maximum_amount" value="{{$discount['maximum_amount']}}" class="form-control" placeholder="{{ translate('maximum_amount') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('script_2')
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

        let selected_type = $("#discount_type").val();

        $(document).ready(function() {
            if (selected_type === 'amount') {
                $("#max_amount_div").hide();
            } else {
                $("#max_amount_div").show();
            }
        });

        function show_item(type) {
            if (type === 'amount') {
                $("#max_amount_div").hide();
            } else {
                $("#max_amount_div").show();
            }
        }

        $(document).ready(function() {
            $('form').on('reset', function(e) {
                $("#max_amount_div").show();
            });
        });

    </script>

@endpush

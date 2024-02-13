@extends('layouts.admin.app')

@section('title', translate('Bulk Export List'))

@section('content')

    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/bulk.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('Products Bulk Export')}}
                </span>
            </h1>
        </div>
        <div class="card">
            <div class="card-body p-2 pt-3">
                <div class="export-steps">
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 1')}}</h5>
                            <p>
                                {{translate('Select Data Type')}}
                            </p>
                        </div>
                    </div>
                    <div class="export-steps-item">
                        <div class="inner">
                            <h5>{{translate('STEP 2')}}</h5>
                            <p>
                                {{translate('Select Data Range and Export')}}
                            </p>
                        </div>
                    </div>
                </div>
                <form class="product-form px-3 pb-3"  action="{{route('admin.product.bulk-export')}}" method="GET" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('type')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="type" id="type" data-placeholder="{{translate('select')}} {{translate('type')}}" class="form-control" required title="Select Type">
                                    <option value="all">{{translate('all')}} {{translate('data')}}</option>
                                    <option value="date_wise">{{translate('date')}} {{translate('wise')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group date_wise">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('from')}} {{translate('date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group date_wise">
                                <label class="input-label text-capitalize" for="exampleFormControlSelect1">{{translate('to')}} {{translate('date')}}<span
                                        class="input-label-secondary"></span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button class="btn btn--reset" type="reset" id="reset">{{translate('clear')}}</button>
                                <button class="btn btn--primary" type="submit" href="{{route('admin.product.bulk-export')}}" title="{{translate('bulk_export')}}">{{translate('export')}}</button>
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
    $(document).on('ready', function (){
        $('.id_wise').hide();
        $('.date_wise').hide();
        $('#type').on('change', function() {
            $('.date_wise').hide();
            $('.'+$(this).val()).show();
            $('#end_date').attr('required', false);
        })

    });

    $(document).ready(function () {
        $("#reset").click(function(){
            $('.date_wise').hide();
            $('#end_date').attr('required', false);
            $('#start_date').attr('max',false);
            $('#end_date').attr('min',false);
        })
    });

    $("#start_date").on("change", function () {
        $('#end_date').attr('min',$(this).val());
        $('#end_date').attr('required', true);
    });

    $("#end_date").on("change", function () {
        $('#start_date').attr('max',$(this).val());
    });
</script>
@endpush

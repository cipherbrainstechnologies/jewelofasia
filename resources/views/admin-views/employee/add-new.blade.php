@extends('layouts.admin.app')

@section('title', translate('Add Employee'))

@push('css_or_js')
<link href="{{asset('public/assets/admin')}}/css/select2.min.css" rel="stylesheet"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/employee.png')}}" class="w--24" alt="mail">
            </span>
            <span>
                {{translate('Add New Employee')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->


    <!-- Content Row -->
    <form action="{{route('admin.employee.add-new')}}" method="post" enctype="multipart/form-data"
            style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-user"></i>
                    </span>
                    <span>{{translate('General Information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">{{translate('Full Name')}}</label>
                                <input type="text" name="name" class="form-control" id="name"
                                        placeholder="{{translate('Ex')}} : {{translate('John Doe')}}" value="{{old('name')}}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{translate('Phone')}}</label>
                                <input type="text" name="phone" value="{{old('phone')}}" class="form-control" id="phone"
                                        placeholder="{{translate('Ex')}} : +88017********" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{translate('Role')}}</label>
                                <select class="form-control" name="role_id"
                                        style="max-width: 100%">
                                    <option value="0" selected disabled>---{{translate('select')}}---</option>
                                    @foreach($rls as $r)
                                        <option value="{{$r->id}}" {{old('role_id')==$r->id?'selected':''}}>{{$r->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('identity')}} {{translate('type')}}</label>
                                <select name="identity_type" class="form-control">
                                    <option value="passport">{{translate('passport')}}</option>
                                    <option value="driving_license">{{translate('driving')}} {{translate('license')}}</option>
                                    <option value="nid">{{translate('nid')}}</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{translate('Identity Number')}}</label>
                                <input type="text" class="form-control" name="identity_number" value="{{old('identity_number')}} "placeholder="{{translate('Identity Number')}} : 674-5596-9854" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label d-none d-md-block ">
                                &nbsp;
                            </label>
                            <center class="mb-4">
                                <img class="initial-24" id="viewer"
                                src="{{asset('public/assets/admin/img/upload-vertical.png')}}" alt="Employee thumbnail"/>
                            </center>
                            <div class="form-group mb-0">
                                <label class="form-label d-block ">
                                    {{ translate('Employee Image') }} <span class="text-danger">({{ translate('Ratio 1:1') }})</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileUpload" class="custom-file-input h--45px"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label  h--45px" for="customFileUpload"></label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-label d-block mb-2 pb-1">
                                {{ translate('Identity Image') }}
                            </label>
                            <div class="product--coba">
                                <div class="row g-2" id="coba"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-user"></i>
                    </span>
                    <span>{{translate('Account Information')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">{{translate('Email')}}</label>
                        <input type="email" name="email" value="{{old('email')}}" class="form-control" id="email"
                                placeholder="{{translate('Ex')}} : ex@gmail.com" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">{{translate('password')}}</label>
                        <input type="text" name="password" class="form-control" id="password" placeholder="{{translate('Ex : 8+ Characters')}}" required>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="form-label">{{translate('Confirm Password')}}</label>
                        <input type="text" name="password_confirmation" class="form-control" id="password" placeholder="{{translate('Ex : 8+ Characters')}}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn--container justify-content-end mt-3">
            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
        </div>
    </form>


</div>
@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 2,
                rowHeight: '140px',
                groupClassName: 'two__item',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/upload-vertical.png')}}',
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
                    toastr.error('{{ translate("Please only input png or jpg type file") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{ translate("File size too big") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>

    <script src="{{asset('public/assets/admin')}}/js/select2.min.js"></script>
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

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush

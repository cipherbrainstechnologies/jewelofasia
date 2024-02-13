@extends('layouts.admin.app')

@section('title', translate('Update Notification'))

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
                    {{translate('notification update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.notification.update',[$notification['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label mb-3" for="exampleFormControlInput1">{{translate('title')}}</label>
                                <input type="text" value="{{$notification['title']}}" name="title" class="form-control" placeholder="{{ translate('New notification') }}" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label mb-3" for="exampleFormControlInput1">{{translate('description')}}</label>
                                <textarea name="description" class="form-control h--92px" required>{{$notification['description']}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <h5 class="text-center mb-3 mt-auto text--title text-capitalize">
                                    {{translate('notification banner')}}
                                    <small class="text-danger">* ( {{translate('ratio')}} 3:1 )</small>
                                </h5>
                                <label class="upload--vertical mt-auto">
                                    <input type="file" name="image" id="customFileEg1" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" hidden>
                                    <img id="viewer" onerror="this.src='{{asset('public/assets/admin/img/upload-vertical.png')}}'" src="{{asset('storage/app/public/notification')}}/{{$notification['image']}}" alt="notification image"/>
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end mt-2">
                                <button type="reset" class="btn btn--reset">{{translate('Reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('Update')}}</button>
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
    </script>
@endpush

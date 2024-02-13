@extends('layouts.admin.app')

@section('title', translate('Update city'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/category.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{translate('city_setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row g-2">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body pt-sm-0 pb-sm-4">
                        <form action="{{route('admin.cities.update',[$city['id']])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @php($data = Helpers::get_business_settings('language'))
                            @php($default_lang = Helpers::get_default_language())
                            {{-- @php($default_lang = 'en') --}}
                            @if ($data && array_key_exists('code', $data[0]))
                                {{-- @php($default_lang = json_decode($language)[0]) --}}
                                <ul class="nav nav-tabs d-inline-flex mb-5">
                                    @foreach ($data as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang['default'] == true ? 'active' : '' }}" href="#"
                                        id="{{ $lang['code'] }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang['code']) . '(' . strtoupper($lang['code']) . ')' }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="row  g-4">
                                    @foreach ($data as $lang)
                                        <?php
                                            if (count($city['translations'])) {
                                                $translate = [];
                                                foreach ($city['translations'] as $t) {
                                                    if ($t->locale == $lang['code'] && $t->key == "name") {
                                                        $translate[$lang['code']]['name'] = $t->value;
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="col-sm-6 {{ $lang['default'] == false ? 'd-none' : '' }} lang_form"
                                                id="{{ $lang['code'] }}-form">
                                            <div class="col-lg-12">
                                                 <label class="form-label"
                                                    for="exampleFormControlInput1">{{translate('city')}} {{ translate('name') }}
                                                ({{ strtoupper($lang['code']) }})</label>
                                                <input type="text" name="name[]" class="form-control" placeholder="{{translate('city')}} {{ translate('name') }}" maxlength="255"
                                                    {{$lang['status'] == true ? 'required':''}} value="{{$city["name"] ?? ""}}"
                                                    @if($lang['status'] == true) oninvalid="document.getElementById('{{$lang['code']}}-link').click()" @endif>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="status">{{ translate('Status') }}</label>
                                            <select name="status" class="form-control" required>
                                                <option value="active" @if($city["status"] == 'active') 'selected' @endif>{{ translate('Active') }}</option>
                                                <option value="inactive" @if($city["status"] == 'inactive') 'selected' @endif>{{ translate('Inactive') }}</option>
                                            </select>
                                        </div>
                                        
                                        <input type="hidden" name="lang[]" value="{{ $lang['code'] }}">
                                    @endforeach
                            @else
                                        <div class="lang_form col-sm-6" id="{{ $default_lang }}-form">
                                            <div class="col-lg-12">
                                                <label class="form-label"
                                                    for="exampleFormControlInput1">{{translate('city')}} {{ translate('name') }}
                                                ({{ strtoupper($default_lang) }})</label>
                                                <input type="text" name="name[]" class="form-control" maxlength="255"
                                                    placeholder="{{translate('city')}} {{ translate('name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="status">{{ translate('Status') }}</label>
                                            <select name="status" class="form-control" required>
                                                <option value="active">{{ translate('Active') }}</option>
                                                <option value="inactive">{{ translate('Inactive') }}</option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                                    @endif
                                    
                                    <div class="col-12">
                                        <div class="btn--container justify-content-end">
                                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                        </div>
                                    </div>
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

        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>

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
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>

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

@extends('layouts.admin.app')

@section('title', translate('social media links'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/social.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('social media Links')}}
                </span>
            </h1>
        </div>

        <!-- Content Row -->
        <div class="card">
            <div class="card-body">
                <form>
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="name" class="form-label">{{translate('name')}}</label>
                            <select class="form-control" name="name" id="name" required>
                                <option value="zero">---{{translate('select')}}---</option>
                                <option value="instagram">{{translate('Instagram')}}</option>
                                <option value="facebook">{{translate('Facebook')}}</option>
                                <option value="twitter">{{translate('Twitter')}}</option>
                                <option value="linkedin">{{translate('LinkedIn')}}</option>
                                <option value="pinterest">{{translate('Pinterest')}}</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <input type="hidden" id="id">
                            <label for="link" class="form-label {{Session::get('direction') === "rtl" ? 'mr-1' : ''}}">{{ translate('social_media_link')}}</label>
                            <input type="text" name="link" class="form-control" id="link"
                                    placeholder="{{translate('Enter Social Media Link')}}" required>
                        </div>
                        <input type="hidden" id="id">
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                        <button id="add" class="btn btn--primary" style="color: white">{{ translate('submit')}}</button>
                        <a id="update" class="btn btn--primary"
                        style="display: none; color: #fff;">{{ translate('update')}}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0" id="dataTable" width="100%" cellspacing="0"
                            style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <thead class="thead-light">
                        <tr>
                            <th>{{ translate('sl')}}</th>
                            <th>{{ translate('name')}}</th>
                            <th>{{ translate('link')}}</th>
                            <th>{{ translate('status')}}</th>
                            <th class="text-center">{{ translate('action')}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <script>
        fetch_social_media();

        function fetch_social_media() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.web-app.third-party.fetch')}}",
                method: 'GET',
                success: function (data) {

                    if (data.length != 0) {
                        var html = '';
                        for (var count = 0; count < data.length; count++) {
                            html += '<tr>';
                            html += '<td class="column_name" data-column_name="sl" data-id="' + data[count].id + '">' + (count + 1) + '</td>';
                            html += '<td class="column_name" data-column_name="name" data-id="' + data[count].id + '">' + data[count].name + '</td>';
                            html += '<td class="column_name" data-column_name="slug" data-id="' + data[count].id + '">' + data[count].link + '</td>';
                            html += `<td class="column_name" data-column_name="status" data-id="${data[count].id}">
                                <label class="toggle-switch toggle-switch-sm">
                                    <input type="checkbox" class="toggle-switch-input status" id="${data[count].id}" ${data[count].status == 1 ? "checked" : ""} >
                                    <span class="toggle-switch-label">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>`;
                            // html += '<td><a type="button" class="btn btn-primary btn-xs edit" id="' + data[count].id + '"><i class="fa fa-edit text-white"></i></a> <a type="button" class="btn btn-danger btn-xs delete" id="' + data[count].id + '"><i class="fa fa-trash text-white"></i></a></td></tr>';
                            html += '<td><a type="button" class="action-btn edit mx-auto" id="' + data[count].id + '"><i class="tio-edit"></i></a> </td></tr>';
                        }
                        $('tbody').html(html);
                    }
                }
            });
        }

        $('#add').on('click', function () {
            // $('#add').attr("disabled", true);
            var name = $('#name').val();
            var link = $('#link').val();
            if (name === 'zero') {
                toastr.error('{{translate('Social Name Is Requeired')}}.');
                return false;
            }

            if (link == "") {
                toastr.error('{{translate('Social Link Is Requeired')}}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.web-app.third-party.social-media-store')}}",
                method: 'POST',
                data: {
                    name: name,
                    link: link
                },
                success: function (response) {
                    if (response.error == 1) {
                        toastr.error('{{translate('Social Media Already taken')}}');
                    } else {
                        toastr.success('{{translate('Social Media inserted Successfully')}}.');
                    }
                    $('#name').val('');
                    $('#link').val('');
                    fetch_social_media();
                }
            });
        });
        $('#update').on('click', function () {
            $('#update').attr("disabled", true);
            var id = $('#id').val();
            var name = $('#name').val();
            var link = $('#link').val();

            if (name === 'zero') {
                console.log(name);
                toastr.error('{{translate('Social Name Is Requeired')}}.');
                return false;
            }
            if (link == null) {
                toastr.error('{{translate('Social Link Is Requeired')}}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.web-app.third-party.social-media-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    name: name,
                    link: link,
                },
                success: function (data) {
                    $('#name').val('');
                    $('#link').val('');

                    if (data.error == 1) {
                        toastr.error('{{translate('Social Media Already taken')}}');
                    } else {
                        toastr.success('{{translate('Social info updated Successfully')}}.');
                    }

                    $('#update').hide();
                    $('#add').show();
                    fetch_social_media();

                }
            });
            $('#save').hide();
        });
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            if (confirm("{{translate('Are you sure delete this social media')}}?")) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.business-settings.web-app.third-party.social-media-delete')}}",
                    method: 'POST',
                    data: {id: id},
                    success: function (data) {
                        fetch_social_media();
                        toastr.success('{{translate('Social media deleted Successfully')}}.');
                    }
                });
            }
        });
        $(document).on('click', '.edit', function () {
            $('#update').show();
            $('#add').hide();
            var id = $(this).attr("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.web-app.third-party.social-media-edit')}}",
                method: 'POST',
                data: {id: id},
                success: function (data) {
                    $(window).scrollTop(0);
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#link').val(data.link);
                    fetch_social_media()
                }
            });
        });
        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.web-app.third-party.social-media-status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
    </script>
@endpush

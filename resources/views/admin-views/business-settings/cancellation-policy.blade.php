@extends('layouts.admin.app')

@section('title', translate('Cancellation policy'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            @include('admin-views.business-settings.partial.page-setup-menu')

            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h5 class="d-flex flex-wrap justify-content-end">
                        <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                            <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">{{ translate('on') }}</span>
                            <span class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('Status') }}</span>
                            <input type="checkbox"
                                   onclick="status_change_alert('{{ route('admin.business-settings.page-setup.cancellation-policy.status', [$status['value'] ? 0 : 1]) }}', '{{ $status['value']? translate('you want to disable this page'): translate('you want to active this page') }}', event)"
                                   class="toggle-switch-input" id="stocksCheckbox"
                                {{ $status['value'] ? 'checked' : '' }}>
                            <span class="toggle-switch-label text">
                                <span class="toggle-switch-indicator"></span>
                            </span>
                        </label>
                    </h5>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.page-setup.cancellation-policy')}}" method="post" id="tnc-form">
                    @csrf
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="cancellation_policy">{!! $data['value'] !!}</textarea>
                    </div>

                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });

        $('#reset').click(function() {
            location.reload();
        });
    </script>

    <script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate("Are you sure?") }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: '{{ translate("No") }}',
                confirmButtonText: '{{ translate("Yes") }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
    </script>
@endpush

@extends('layouts.admin.app')

@section('title', translate('branch List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/add_branch.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('branch List')}} <span class="badge badge-soft-secondary">{{ $branches->total() }}</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card--header">
                            <h5 class="card-header-title"></h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search by Name')}}" aria-label="Search"
                                        value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{translate('Search')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true
                               }'>
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('#')}}</th>
                                <th class="border-0">{{translate('branch name')}}</th>
                                <th class="border-0">{{translate('branch type')}}</th>
                                <th class="border-0">{{translate('contact info')}}</th>
                                <th class="border-0">{{translate('status')}}</th>
                                <th class="border-0 text-center">{{translate('action')}}</th>
                            </tr>

                            </thead>

                            <tbody>
                            @foreach($branches as $key=>$branch)
                                <tr>
                                    <td>{{$branches->firstItem()+$key}}</td>
                                    <td>
                                        <div class="short-media">
                                            <img onerror="this.src='{{asset('public/assets/admin/img/store-1.png')}}'"
                                                 src="{{asset('storage/app/public/branch')}}/{{$branch['image']}}">
                                            <div class="text-cont">
                                                <span class="d-block font-size-sm text-body text-trim-50">
                                                    {{$branch['name']}}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($branch['id']==1)
                                            <span class="">{{translate('main')}} </span>
                                        @else
                                            <span class="">{{translate('sub Branch')}} </span>
                                        @endif
                                    </td>
                                    <td>
                                        <h5 class="m-0">
                                            <a href="mailto:{{$branch['email']}}">{{$branch['email']}}</a>
                                        </h5>
                                        <div>
                                            <a href="Tel:{{$branch['phone']}}">{{$branch['phone']}}</a>
                                        </div>
                                    </td>
                                    <td>
                                        @if($branch['id']!=1)
                                        <label class="toggle-switch">
                                            <input type="checkbox"
                                                   onclick="status_change_alert('{{ route('admin.branch.status', [$branch->id, $branch->status ? 0 : 1]) }}', '{{ $branch->status? translate('you_want_to_disable_this_branch'): translate('you_want_to_active_this_branch') }}', event)"
                                                   class="toggle-switch-input" id="stocksCheckbox{{ $branch->id }}"
                                                {{ $branch->status ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                        </label>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.branch.edit',[$branch['id']])}}"><i class="tio-edit"></i>
                                            </a>
                                            @if($branch['id']!=1)
                                                <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                                    onclick="form_alert('branch-{{$branch['id']}}','{{ translate("Want to delete this") }}')">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <form action="{{route('admin.branch.delete',[$branch['id']])}}"
                                                method="post" id="branch-{{$branch['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $branches->links() !!}
                            </tfoot>
                        </table>
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
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush

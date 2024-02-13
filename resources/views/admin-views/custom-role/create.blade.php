@extends('layouts.admin.app')

@section('title', translate('employee role'))

@push('css_or_js')

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
                {{translate('Employee Role Setup')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->

    <!-- Content Row -->
    <div class="card mb-3">
        <div class="card-body">
            <form id="submit-create-role" method="post" action="{{route('admin.custom-role.store')}}"
                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                @csrf
                <div class="max-w-500px">
                    <div class="form-group">
                        <label class="form-label">{{translate('role_name')}}</label>
                        <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp" placeholder="{{translate('Ex')}} : {{translate('Store')}}" required>
                    </div>
                </div>

<!--                <div class="d-flex">
                    <h5 class="input-label m-0 text-capitalize">{{translate('module_permission')}} : </h5>
                    <div class="check-item pb-0 w-auto">
                        <div class="form-group form-check form&#45;&#45;check m-0 ml-2">
                            <input type="checkbox" name="modules[]" value="account" class="form-check-input"
                                    id="select-all">
                            <label class="form-check-label ml-2" for="select-all">{{ translate('Select All') }}</label>
                        </div>
                    </div>
                </div>-->

                <div class="d-flex">
                    <h5 class="input-label m-0 text-capitalize">{{translate('module_permission')}} : </h5>
                    <div class="check-item pb-0 w-auto">
                        <input type="checkbox" id="select_all">
                        <label class="title-color mb-0 pl-2" for="select_all">{{ translate('select_all')}}</label>
                    </div>
                </div>

                <div class="check--item-wrapper">
                    @foreach(MANAGEMENT_SECTION as $section)
                        <div class="check-item">
                            <div class="form-group form-check form--check">
                                <input type="checkbox" name="modules[]" value="{{$section}}" class="form-check-input module-permission" id="{{$section}}">
                                <label class="form-check-label" style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};" for="{{$section}}">{{translate($section)}}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="btn--container justify-content-end mt-4">
                    <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('Submit')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header">
                <h5 class="card-title">{{translate('employee_roles_table')}} <span class="badge badge-soft-primary">{{count($rl)}}</span></h5>
                <form action="{{url()->current()}}" method="GET">
                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search"
                            class="form-control"
                            placeholder="{{translate('Search by Role Name')}}" aria-label="Search" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text">
                                {{translate('Search')}}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="hs-unfold ml-sm-3">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn btn-outline-primary-2 btn--primary font--sm" href="javascript:;"
                        data-hs-unfold-options='{
                            "target": "#usersExportDropdown",
                            "type": "css-animation"
                        }'>
                        <i class="tio-download-to mr-1"></i> {{translate('export')}}
                    </a>

                    <div id="usersExportDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                        <span class="dropdown-header">{{translate('download')}} {{translate('options')}}</span>
                        <a id="export-excel" class="dropdown-item" href="{{route('admin.custom-role.export')}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2" src="{{asset('public/assets/admin')}}/svg/components/excel.svg" alt="Image Description">
                            {{translate('excel')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0" id="dataTable" cellspacing="0" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('role_name')}}</th>
                        <th>{{translate('modules')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rl as $k=>$r)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$r['name']}}</td>
                            <td class="text-capitalize">
                                <div class="max-w-300px">
                                    @if($r['module_access']!=null)
                                        @php($comma = '')
                                        @foreach((array)json_decode($r['module_access']) as $m)
                                            {{$comma}}{{ translate(str_replace('_',' ',$m)) }}
                                            @php($comma = ', ')
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                           onclick="status_change_alert('{{ route('admin.custom-role.status', [$r->id, $r->status ? 0 : 1]) }}', '{{ $r->status? translate('you_want_to_disable_this_role'): translate('you_want_to_active_this_role') }}', event)"
                                           class="toggle-switch-input" id="stocksCheckbox{{ $r->id }}"
                                        {{ $r->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="{{route('admin.custom-role.update',[$r['id']])}}"
                                        class="action-btn"
                                        title="{{translate('Edit') }}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                        onclick="form_alert('role-{{$r['id']}}','{{translate('Want to delete this role ?')}}')">
                                        <i class="tio-delete-outlined"></i></a>
                                    <form action="{{route('admin.custom-role.delete',[$r['id']])}}"
                                          method="post" id="role-{{$r['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($rl) === 0)
                    <div class="text-center p-4">
                        <img class="mb-3" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description" style="width: 7rem;">
                        <p class="mb-0">No data to show</p>
                    </div>
                @endif
            </div>
        </div>
        <div>
            {{$rl->links()}}
        </div>
    </div>
</div>
@endsection

@push('script_2')


<script>
    $(document).ready(function() {
        // Check or uncheck "Select All" based on other checkboxes
        $(".module-permission").on('change', function (){
            if ($(".module-permission:checked").length == $(".module-permission").length) {
                $("#select_all").prop("checked", true);
            } else {
                $("#select_all").prop("checked", false);
            }
        });

        // Check or uncheck all checkboxes based on "Select All" checkbox
        $("#select_all").on('change', function (){
            if ($("#select_all").is(":checked")) {
                $(".module-permission").prop("checked", true);
            } else {
                $(".module-permission").prop("checked", false);
            }
        });

        // Check "Select All" checkbox on page load if all checkboxes are checked
        if ($(".module-permission:checked").length == $(".module-permission").length) {
            $("#select_all").prop("checked", true);
        }
    });
</script>
@endpush

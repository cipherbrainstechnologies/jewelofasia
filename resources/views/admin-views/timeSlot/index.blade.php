@extends('layouts.admin.app')

@section('title', translate('Add new Time Slot'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        @include('admin-views.business-settings.partial.business-settings-navmenu')
        <div class="card mb-2">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-clock"></i>
                            </span> <span>{{translate('Time Slot')}}</span>
                        </h5>
                    </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.store.timeSlot.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label"> {{translate('Time')}} {{translate('Start')}} </label>
                                <input type="time" name="start_time" class="form-control" value="10:30:00"
                                       placeholder="{{ translate('Ex : 10:30 am') }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label"> {{translate('Time')}} {{translate('Ends')}} </label>
                                <input type="time" name="end_time" class="form-control" value="19:30:00" placeholder="{{ translate("5:45 pm") }}"
                                       required>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>

        </div>
        <div class="card">
            <div class="card-header border-0 py-1">
                <h5 class="card-header-title py-3 d-flex align-items-center">
                    <span>{{translate('Time Slot List')}}</span> <span class="ml-1 badge-pill py-1 px-2 badge badge-soft-secondary">{{count($timeSlots)}}</span>
                </h5>
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
                        <th class="text-center">{{translate('#')}}</th>
                        <th class="text-center">{{translate('Start')}} {{translate('Time')}} </th>
                        <th class="text-center">{{translate('End')}} {{translate('Time')}}  </th>
                        <th class="text-center">{{translate('duration')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($timeSlots as $key=>$timeSlot)
                        <tr>
                            <td class="text-center">{{$key+1}}</td>
                            <td class="text-center">
                                <div>{{ date(config('time_format'), strtotime($timeSlot['start_time'])) }}</div>
                            </td>
                            <td class="text-center">
                                <div>{{ date(config('time_format'), strtotime($timeSlot['end_time'])) }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $start_time = new DateTime($timeSlot['start_time']);
                                    $duration = $start_time->diff(new DateTime($timeSlot['end_time']));
                                    echo $duration->h.' hours '. $duration->i. ' minutes';
                                @endphp
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                        onclick="status_change_alert('{{ route('admin.business-settings.store.timeSlot.status', [$timeSlot->id, $timeSlot->status ? 0 : 1]) }}', '{{ $timeSlot->status? translate('you_want_to_disable_this_timeSlot'): translate('you_want_to_active_this_timeslot') }}', event)"
                                        class="toggle-switch-input" id="stocksCheckbox{{ $timeSlot->id }}"
                                        {{ $timeSlot->status ? 'checked' : '' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <!-- Dropdown -->
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn"
                                        href="{{route('admin.business-settings.store.timeSlot.update',[$timeSlot['id']])}}">
                                    <i class="tio-edit"></i></a>
                                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                        onclick="form_alert('timeSlot-{{$timeSlot['id']}}','{{ translate("Want to delete this") }}')">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.business-settings.store.timeSlot.delete',[$timeSlot['id']])}}"
                                        method="post" id="timeSlot-{{$timeSlot['id']}}">
                                    @csrf @method('delete')
                                </form>
                                <!-- End Dropdown -->
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- End Table -->
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
@endpush

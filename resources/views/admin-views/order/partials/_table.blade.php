@if(!empty($orders) && $orders->count())
@foreach($orders as $key=>$order)

    <tr class="status-{{$order['order_status']}} class-all">
        <td class="">
            {{$key+1}}
        </td>
        <td class="table-column-pl-0">
            <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
        </td>
        <td>{{date('d M Y',strtotime($order['delivery_date']))}}</td>
        <td>
            <span>{{$order->time_slot?date(config('time_format'), strtotime($order->time_slot['start_time'])).' - ' .date(config('time_format'), strtotime($order->time_slot['end_time'])) :'No Time Slot'}}</span>

        </td>
        <td>
            @if($order->customer)
                <div>
                    <a class="text-body text-capitalize font-medium"
                    href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                </div>
                <div class="text-sm">
                    <a href="Tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                </div>
            @else
                <label
                    class="badge badge-danger">{{translate('invalid')}} {{translate('customer')}} {{translate('data')}}</label>
            @endif
        </td>
        <td>
            <label
                class="badge badge-soft-primary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
        </td>

        <td>
            <div class="mw-90">
                {{ Helpers::set_symbol($order['order_amount']) }}
            </div>
        </td>
        <td class="text-capitalize text-center">
            @if($order['order_status']=='pending')
                <span class="badge badge-soft-info">
                    {{translate('pending')}}
                </span>
            @elseif($order['order_status']=='confirmed')
                <span class="badge badge-soft-info">
                    {{translate('confirmed')}}
                </span>
            @elseif($order['order_status']=='processing')
                <span class="badge badge-soft-warning">
                    {{translate('packaging')}}
                </span>
            @elseif($order['order_status']=='out_for_delivery')
                <span class="badge badge-soft-warning">
                    {{translate('out_for_delivery')}}
                </span>
            @elseif($order['order_status']=='delivered')
                <span class="badge badge-soft-success">
                    {{translate('delivered')}}
                </span>
            @else
                <span class="badge badge-soft-danger">
                    {{str_replace('_',' ',$order['order_status'])}}
                </span>
            @endif
        </td>
        <td>
            <div class="btn--container justify-content-center">
                <a class="action-btn btn--primary btn-outline-primary" href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i class="tio-invisible"></i></a>
                <a class="action-btn" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                    <i class="tio-download-to"></i>
                </a>
            </div>
        </td>
    </tr>


@endforeach
@else
<tr>
<td colspan="4">No data found.</td>
</tr>
@endif



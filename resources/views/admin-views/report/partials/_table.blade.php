<table class="table table-borderless table-align-middle">
    <thead class="thead-light">
    <tr>
        <th>{{translate('#')}} </th>
        <th>{{translate('product info')}}</th>
        <th>{{translate('qty')}}</th>
        <th>{{translate('date')}}</th>
        <th>{{translate('amount')}}</th>
        <th class="text-center">{{translate('action')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $key=>$row)
        <tr>
            <td>
                {{$key+1}}
            </td>
            <td>
                <a href="{{route('admin.product.view',[$row['product_id']])}}" target="_blank" class="product-list-media">
                    <img src="{{asset('storage/app/public/product')}}/{{$row['product_image']}}"
                         onerror="this.src='{{asset('/public/assets/admin/img/160x160/2.png')}}'"
                    />
                    <h6 class="name line--limit-2">
                        {{$row['product_name']}}
                    </h6>
                </a>
            </td>
            <td>
                <span class="badge badge-soft-primary">{{$row['quantity']}}</span>
            </td>
            <td>
                <div>
                    {{date('d M Y',strtotime($row['date']))}}
                </div>
            </td>
            <td>
                <div>
                    {{ Helpers::set_symbol($row['price']) }}
                </div>
            </td>
            <td>
                <div class="btn--container justify-content-center">
                    <a class="action-btn"
                        href="#0">
                    <i class="tio-edit"></i></a>
                    <a class="action-btn btn--danger btn-outline-danger" href="javascript:">
                        <i class="tio-delete-outlined"></i>
                    </a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<!--  -->
@if(count($data) === 0)
<div class="text-center p-4">
    <img class="mb-3 w-120px" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
    <p class="mb-0">No data to show</p>
</div>
@endif
<!--  -->

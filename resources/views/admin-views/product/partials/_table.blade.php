@foreach($products as $key=>$product)
    <tr>
        <td>{{$key+1}}</td>
        <td>
                                        <span class="d-block font-size-sm text-body">
                                             <a href="{{route('admin.product.view',[$product['id']])}}">
                                               {{substr($product['name'],0,20)}}{{strlen($product['name'])>20?'...':''}}
                                             </a>
                                        </span>
        </td>
        <td>
            <div style="height: 100px; width: 100px; overflow-x: hidden;overflow-y: hidden">
                <img
                    src="{{asset('storage/app/public/product')}}/{{json_decode($product['image'],true)[0]}}"
                    style="width: 100px"
                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'">
            </div>
        </td>
        <td>
            @if($product['status']==1)
                <div style="padding: 10px;border: 1px solid;cursor: pointer"
                     onclick="location.href='{{route('admin.product.status',[$product['id'],0])}}'">
                                                <span
                                                    class="legend-indicator bg-success"></span>{{translate('active')}}
                </div>
            @else
                <div style="padding: 10px;border: 1px solid;cursor: pointer"
                     onclick="location.href='{{route('admin.product.status',[$product['id'],1])}}'">
                                                <span
                                                    class="legend-indicator bg-danger"></span>{{translate('disabled')}}
                </div>
            @endif
        </td>
        <td>
            <label class="switch ml-2">
                <input type="checkbox" class="status"
                       onchange="daily_needs('{{$product['id']}}','{{$product->daily_needs==1?0:1}}')"
                       id="{{$product['id']}}" {{$product->daily_needs == 1?'checked':''}}>
                <span class="slider round"></span>
            </label>
        </td>
        <td>{{ Helpers::set_symbol($product['price']) }}</td>
        <td>
            <label class="badge badge-soft-info">{{$product['total_stock']}}</label>
        </td>
        <td>
            <!-- Dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="tio-settings"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item"
                       href="{{route('admin.product.edit',[$product['id']])}}">{{translate('edit')}}</a>
                    <a class="dropdown-item" href="javascript:"
                       onclick="form_alert('product-{{$product['id']}}','{{translate('Want to delete this item ?')}}')">{{translate('delete')}}</a>
                    <form action="{{route('admin.product.delete',[$product['id']])}}"
                          method="post" id="product-{{$product['id']}}">
                        @csrf @method('delete')
                    </form>
                </div>
            </div>
            <!-- End Dropdown -->
        </td>
    </tr>
@endforeach

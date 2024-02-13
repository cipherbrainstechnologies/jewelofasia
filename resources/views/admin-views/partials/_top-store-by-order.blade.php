<!-- End Header -->
<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between flex-grow-1">
        <span>{{translate('top_store_by_order_received')}}</span>
        <a href="" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
    </h5>
</div>
<!-- Body -->
<div class="card-body">
    <div class="row">
        @foreach($top_store_by_order_received as $key=>$item)
            @php($shop=\App\Model\Shop::where('seller_id',$item['seller_id'])->first())
            @if(isset($shop))
                <div class="col-6 col-md-4 mt-2"
                     onclick="location.href='{{route('admin.sellers.view',$item['seller_id'])}}'"
                     style="padding-left: 6px;padding-right: 6px;cursor: pointer">
                    <div class="grid-card" style="min-height: 170px">
                        <label class="label_1">{{ translate('Orders') }} : {{$item['count']}}</label>
                        <center class="mt-6">
                            <img style="border-radius: 50%;width: 60px;height: 60px;border:2px solid #80808082;"
                                 onerror="this.src='{{asset('public/assets/back-end/img/160x160/img1.jpg')}}'"
                                 src="{{asset('storage/app/public/shop/'.$shop->image  ?? '' )}}">
                        </center>
                        <div class="text-center mt-2">
                            <span style="font-size: 10px">{{$shop['name']??'Not exist'}}</span>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
<!-- End Body -->

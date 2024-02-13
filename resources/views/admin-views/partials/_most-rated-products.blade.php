<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between flex-grow-1">
        <span>{{translate('most_rated_products')}}</span>
        <a href="{{route('admin.reviews.list')}}" class="fz-12px font-medium text-006AE5">{{translate('view_all')}}</a>
    </h5>
</div>

<!-- Body -->
<div class="card-body">
    <div class="rated--products">
        @foreach($most_rated_products as $key=>$item)
            @php($product=\App\Model\Product::find($item['product_id']))
            @if(isset($product))
                <a href="{{route('admin.product.view',[$item['product_id']])}}">
                    <div class="rated-media d-flex align-items-center">
                        <img src="{{asset('storage/app/public/product')}}/{{ json_decode($product['image'])[0]  ?? '' }}"
                             onerror="this.src='{{asset('public/assets/admin/img/400x400/img2.jpg')}}'" alt="{{$product->name}} image">
                        <span class="line--limit-1 w-0 flex-grow-1">
                            {{isset($product)?substr($product->name,0,30) . (strlen($product->name)>20?'...':''):'not exists'}}
                        </span>
                    </div>
                    <div class="">
                        <span class="rating text-info"><i class="tio-star"></i></span>
                        <span>{{ $avg_rating = count($product->rating)>0?number_format($product->rating[0]->average, 2, '.', ' '):0 }} </span>
                        ({{$item['total']}})
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>
<!-- End Body -->


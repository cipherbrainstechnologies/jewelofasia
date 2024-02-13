
<div class="product-card card" onclick="quickView('{{$product->id}}')">
    <?php
        $category_id = null;
        foreach (json_decode($product['category_ids'], true) as $cat) {
            if ($cat['position'] == 1){
                $category_id = ($cat['id']);
            }
        }

        $category_discount = \App\CentralLogics\Helpers::category_discount_calculate($category_id, $product['price']);
        $product_discount = \App\CentralLogics\Helpers::discount_calculate($product, $product['price']);

        if ($category_discount >= $product['price']){
            $discount = $product_discount;
        }else{
            $discount = max($category_discount, $product_discount);
        }
    ?>
    <div class="card-header inline_product clickable p-0">
    @if (!empty(json_decode($product['image'],true)))
        <img src="{{asset('storage/app/public/product')}}/{{json_decode($product['image'], true)[0]}}"
            onerror="this.src='{{asset('public/assets/admin/img/160x160/2.png')}}'" class="w-100 h-100 object-cover aspect-ratio-80">
    @else
        <img src="{{asset('public/assets/admin/img/160x160/2.png')}}" class="w-100 h-100 object-cover aspect-ratio-80"
        >
    @endif
    </div>

    <div class="card-body inline_product text-center p-1 clickable">
        <div style="position: relative;" class="product-title1 text-dark font-weight-bold">
            {{ Str::limit($product['name'], 12) }}
        </div>
        <div class="justify-content-between text-center">
            <div class="product-price text-center">
                {{ Helpers::set_symbol($product['price'] - $discount) }}
            </div>
        </div>
    </div>
</div>


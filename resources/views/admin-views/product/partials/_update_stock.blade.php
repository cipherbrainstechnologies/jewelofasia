<div class="card-header">
    <h4 class="m-0">{{translate('Product price & stock')}}</h4>
    <input name="product_id" value="{{$product['id']}}" class="d-none">
</div>
<div class="card-body pt-0">
    <div class="sku_combination" id="sku_combination">
        @include('admin-views.product.partials._edit_sku_combinations',['combinations'=>json_decode($product['variations'],true)])
    </div>
    <div id="quantity" class="mt-3">
        <label class="control-label">{{translate('total')}} {{translate('Quantity')}}</label>
        <input type="number" min="0" value={{ $product->total_stock }} step="1"
                placeholder="{{translate('Quantity') }}"
                name="total_stock" class="form-control" required>
    </div>
</div>

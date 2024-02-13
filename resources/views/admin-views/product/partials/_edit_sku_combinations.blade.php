@if(count($combinations) > 0)
    <table class="table table-bordered physical_product_show">
        <thead>
        <tr>
            <td class="text-center border-bottom-0">
                <label class="title-color m-0">{{translate('Variant')}}</label>
            </td>
            <td class="text-center border-bottom-0">
                <label class="title-color m-0">{{translate('Variant Price')}}</label>
            </td>
            <td class="text-center border-bottom-0">
                <label class="title-color m-0">{{translate('Quantity')}}</label>
            </td>
        </tr>
        </thead>
        <tbody>
        @endif
        @foreach ($combinations as $key => $combination)
            <tr>
                <td>
                    <label class="control-label m-0">{{ $combination['type'] }}</label>
                    <input value="{{ $combination['type'] }}" name="type[]" style="display: none">
                </td>
                <td>
                    <label for="" class="control-label">{{ $combination['price'] }}</label>
                    <input type="number" name="price_{{ $combination['type'] }}"
                           value="{{ ($combination['price']) }}" min="0"
                           step="0.01"
                           class="form-control" style="display: none">
                </td>
                <td>
                    <input type="number" onkeyup="update_qty()" name="qty_{{ $combination['type'] }}" value="{{ $combination['stock'] }}" min="1" max="100000" step="1"
                           class="form-control"
                           required>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


@if(count($combinations[0]) > 0)
    <table class="table table-bordered">
        <thead>
        <tr>
            <td class="text-center">
                <label for="" class="control-label">{{ translate('Variant') }}</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">{{ translate('Variant Price') }}</label>
            </td>
            <td class="text-center">
                <label for="" class="control-label">{{ translate('Variant Stock') }}</label>
            </td>
        </tr>
        </thead>
        <tbody>

        @foreach ($combinations as $key => $combination)
            @php
                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                    }
                    else{
                        $str .= str_replace(' ', '', $item);
                    }
                }
            @endphp
            @if(strlen($str) > 0)
                <tr>
                    <td>
                        <label for="" class="control-label">{{ $str }}</label>
                     </td>
                    <td>
                        <input type="number" name="price_{{ $str }}" value="{{ $price }}" min="0" step="any"
                               class="form-control" required>
                    </td>
                    <td>
                        <input type="number" name="stock_{{ $str }}" value="0" min="0" max="1000000"
                               class="form-control" onkeyup="update_qty()" required>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endif

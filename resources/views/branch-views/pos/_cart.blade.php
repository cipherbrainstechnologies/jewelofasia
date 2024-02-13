<div class="d-flex flex-row cart--table-scroll">
    <div class="table-responsive">
        <table class="table table-bordered border-left-0 border-right-0 middle-align">
        <thead class="thead-light">
        <tr>
            <th scope="col">{{translate('item')}}</th>
            <th scope="col" class="text-center">{{translate('qty')}}</th>
            <th scope="col">{{translate('price')}}</th>
            <th scope="col">{{translate('delete')}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $subtotal = 0;
        $discount = 0;
        $discount_type = 'amount';
        $discount_on_product = 0;
        $total_tax = 0;
        $updated_total_tax=0;
        $vat_status = \App\CentralLogics\Helpers::get_business_settings('product_vat_tax_status') === 'included' ? 'included' : 'excluded';
        ?>
        @if(session()->has('cart') && count( session()->get('cart')) > 0)
            <?php
            $cart = session()->get('cart');
            if (isset($cart['discount'])) {
                $discount = $cart['discount'];
                $discount_type = $cart['discount_type'];
            }
            ?>
            @foreach(session()->get('cart') as $key => $cartItem)
                @if(is_array($cartItem))
                    <?php
                    $product_subtotal = ($cartItem['price']) * $cartItem['quantity'];
                    $discount_on_product += ($cartItem['discount'] * $cartItem['quantity']);
                    $subtotal += $product_subtotal;

                    //tax calculation
                    $product = \App\Model\Product::find($cartItem['id']);
                    $total_tax += \App\CentralLogics\Helpers::tax_calculate($product, $cartItem['price']) * $cartItem['quantity'];
                    $updated_total_tax += $vat_status === 'included' ? 0 : \App\CentralLogics\Helpers::tax_calculate($product, $cartItem['price']) * $cartItem['quantity'];

                    ?>
                    <tr>
                        <td>
                            <div class="media align-items-center">
                                @if (!empty(json_decode($cartItem['image'],true)))
                                    <img class="avatar avatar-sm mr-1"
                                         src="{{asset('storage/app/public/product')}}/{{json_decode($cartItem['image'], true)[0]}}"
                                         onerror="this.src='{{asset('public/assets/admin/img/160x160/2.png')}}'"
                                         alt="{{$cartItem['name']}} image">
                                @else
                                    <img class="avatar avatar-sm mr-1"
                                         src="{{asset('public/assets/admin/img/160x160/2.png')}}">
                                @endif
                                <div class="media-body">
                                    <h6 class="text-hover-primary mb-0">{{Str::limit($cartItem['name'], 10)}}</h6>
                                    <small>{{Str::limit($cartItem['variant'], 20)}}</small>
                                </div>
                            </div>
                        </td>
                        <td class="align-items-center text-center">
                            <input type="number" data-key="{{$key}}" class="amount--input form-control text-center" id="{{ $cartItem['id'] }}"
                                   value="{{$cartItem['quantity']}}" min="1" max="{{ $product['total_stock'] }}" onkeyup="updateQuantity(event)">
                        </td>
                        <td class="text-center px-0 py-1">
                            <div class="btn text-left">
                                {{ Helpers::set_symbol($product_subtotal) }}
                            </div> <!-- price-wrap .// -->
                        </td>
                        <td class="align-items-center text-center">
                            <div class="d-flex flex-wrap justify-content-center">
                                <a href="javascript:removeFromCart({{$key}})" class="btn btn-sm btn--danger rounded-full action-btn"> <i class="tio-delete-outlined"></i></a>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif
        </tbody>
    </table>
    </div>
</div>

<?php
$total = $subtotal;
$session_total = $subtotal+$total_tax-$discount_on_product;
\Session::put('total', $session_total);

$discount_amount = ($discount_type == 'percent' && $discount > 0) ? (($total * $discount) / 100) : $discount;
$discount_amount += $discount_on_product;
$total -= $discount_amount;

$extra_discount = session()->get('cart')['extra_discount'] ?? 0;
$extra_discount_type = session()->get('cart')['extra_discount_type'] ?? 'amount';
if ($extra_discount_type == 'percent' && $extra_discount > 0) {
    //$extra_discount = (($total + $total_tax) * $extra_discount) / 100;
    $extra_discount = ($total * $extra_discount) / 100;
}
if ($extra_discount) {
    $total -= $extra_discount;
}

$delivery_charge = 0;
if (session()->get('order_type') == 'home_delivery'){
    $distance = 0;
    if (session()->has('address')){
        $address = session()->get('address');
        $distance = $address['distance'];
    }
    $delivery_type = \App\CentralLogics\Helpers::get_business_settings('delivery_management');
    if ($delivery_type['status'] == 1){
        $delivery_charge = \App\CentralLogics\Helpers::get_delivery_charge($distance);
    }else{
        $delivery_charge = \App\CentralLogics\Helpers::get_business_settings('delivery_charge');
    }
}else{
    $delivery_charge = 0;
}

?>
<div class="box p-3">
    <dl class="row">
        <dt class="col-sm-6">{{translate('sub_total')}} :</dt>
        <dd class="col-sm-6 text-right">{{ Helpers::set_symbol($subtotal) }}</dd>


        <dt class="col-sm-6">{{translate('product')}} {{translate('discount')}}:
        </dt>
        <dd class="col-sm-6 text-right"> - {{ Helpers::set_symbol(round($discount_amount,2)) }}</dd>

        <dt class="col-sm-6">{{translate('extra')}} {{translate('discount')}}:
        </dt>
        <dd class="col-sm-6 text-right">
            <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#add-discount"><i
                    class="tio-edit"></i>
            </button> - {{ Helpers::set_symbol($extra_discount) }}</dd>

        <dt class="col-sm-6">{{translate('Delivery Charge')}} :</dt>
        <dd class="col-sm-6 text-right">{{ Helpers::set_symbol(round($delivery_charge, 2)) }}</dd>
        <dt class="col-12">
            <hr class="mt-0">
        </dt>
        <dt class="col-sm-6">{{translate('total')}} :</dt>
        <dd class="col-sm-6 text-right h4 b">{{ Helpers::set_symbol(round($total+$updated_total_tax+$delivery_charge, 2)) }}</dd>
    </dl>
    <div>
        <form action="{{route('branch.pos.order')}}" id='order_place' method="post">
            @csrf
            <div class="pos--payment-options mt-3 mb-3">
                <h5 class="mb-3">{{ translate('Payment Method') }}</h5>
                <ul>
                    <li style="display: {{ !session()->has('order_type') || session('order_type') == 'take_away' ?  'block' : 'none' }}">
                        <label>
                            <input type="radio" name="type" value="cash" hidden="" {{ !session()->has('order_type') || session('order_type') == 'take_away' ? 'checked' : '' }}>
                            <span>{{translate('cash')}}</span>
                        </label>
                    </li>
                    <li style="display: {{ !session()->has('order_type') || session('order_type') == 'take_away' ?  'block' : 'none' }}">
                        <label>
                            <input type="radio" name="type" value="card" hidden="">
                            <span>{{translate('card')}}</span>
                        </label>
                    </li>
                    <li style="display: {{ session('order_type') == 'home_delivery' ?  'block' : 'none' }}">
                        <label>
                            <input type="radio" name="type" value="cash_on_delivery" hidden="" {{ session('order_type') == 'home_delivery' ? 'checked' : '' }}>
                            <span>{{translate('cash_on_delivery')}}</span>
                        </label>
                    </li>
                </ul>
            </div>
            <div class="row button--bottom-fixed g-1 bg-white ">
                <div class="col-sm-6">
                    <a href="#" class="btn btn-outline-danger btn--danger btn-sm btn-block" onclick="emptyCart()"><i
                            class="fa fa-times-circle "></i> {{translate('Cancel Order')}} </a>
                </div>
                <div class="col-sm-6">
                    <!--                    <button type="button" class="btn  btn&#45;&#45;primary btn-sm btn-block" data-toggle="modal"
                            data-target="#paymentModal"><i class="fa fa-shopping-bag"></i>
                        {{translate('Place Order')}}
                    </button>-->
                    <button type="submit" class="btn  btn--primary btn-sm btn-block"><i class="fa fa-shopping-bag"></i>
                        {{translate('Place Order')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="add-discount" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('update_discount')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('branch.pos.discount')}}" method="post" class="row">
                    @csrf
                    <div class="form-group col-sm-6">
                        <label for="">{{translate('discount')}}</label>
                        <input type="number" min="0" max="" value="{{session()->get('cart')['extra_discount'] ?? 0}}"
                               class="form-control" id="extra_discount_input" name="discount" step="any" placeholder="{{translate('Ex: 45')}}">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">{{translate('type')}}</label>
                        <select name="type" class="form-control" id="discount_type_select">
                            <option
                                value="amount" {{$extra_discount_type=='amount'?'selected':''}}>{{translate('amount')}}
                                ({{\App\CentralLogics\Helpers::currency_symbol()}})
                            </option>
                            <option
                                value="percent" {{$extra_discount_type=='percent'?'selected':''}}>{{translate('percent')}}
                                (%)
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn-sm btn--reset" type="reset">{{translate('reset')}}</button>
                            <button class="btn btn-sm btn--primary" type="submit">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-tax" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('update_tax')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('branch.pos.tax')}}" method="POST" class="row">
                    @csrf
                    <div class="form-group col-12">
                        <label for="">{{translate('tax')}} (%)</label>
                        <input type="number" class="form-control" name="tax" min="0">
                    </div>

                    <div class="col-sm-12">
                        <div class="btn--container">
                            <button class="btn btn-sm btn--reset" type="reset">{{translate('reset')}}</button>
                            <button class="btn btn-sm btn--primary" type="submit">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{translate('payment')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('branch.pos.order')}}" id='order_place' method="post" class="row">
                    @csrf
                    <div class="form-group col-12">
                        <label class="input-label" for="">{{ translate('amount') }}({{\App\CentralLogics\Helpers::currency_symbol()}}
                            )</label>
                        <input type="number" class="form-control" name="amount" min="0" step="0.01"
                               value="{{round($total+$total_tax, 2)}}" disabled>
                    </div>
                    <div class="form-group col-12">
                        <label class="input-label" for="">{{translate('type')}}</label>
                        <select name="type" class="form-control">
                            <option value="cash">{{translate('cash')}}</option>
                            <option value="card">{{translate('card')}}</option>
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <div class="btn&#45;&#45;container">
                            <button class="btn btn-sm btn&#45;&#45;reset" type="reset">{{translate('reset')}}</button>
                            <button class="btn btn-sm btn&#45;&#45;primary" type="submit">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>-->



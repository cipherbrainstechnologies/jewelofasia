<div class="modal-body position-relative">
    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="modal--media">
        <!-- Product gallery-->
        <div class="modal--media-avatar">
            @if (!empty(json_decode($product['image'],true)))
                <img src="{{asset('storage/app/public/product')}}/{{json_decode($product['image'], true)[0]}}"
                 onerror="this.src='{{asset('public/assets/admin/img/160x160/2.png')}}'"
                 data-zoom="{{asset('storage/app/public/product')}}/{{json_decode($product['image'], true)[0]}}"
                 alt="Product image" width="">
            @else
                 <img src="{{asset('public/assets/admin/img/160x160/2.png')}}">
             @endif
            <div class="cz-image-zoom-pane"></div>
        </div>
        <!-- Product details-->
        <div class="details">
            <span class="product-name"><a href="#" class="h3 mb-2 product-title">{{ Str::limit($product->name, 100) }}</a></span>

            <div class="mb-3 text-dark">
                <span class="h3 font-weight-normal text-accent mr-1">
                    {{ Helpers::set_symbol($product['price']- $discount) }}
                </span>
                @if($discount > 0)
                    <strike style="font-size: 12px!important;">
                        {{ Helpers::set_symbol($product['price']) }}
                    </strike>
                @endif
            </div>

            @if($discount > 0)
                <div class="mb-3 text-dark">
                    <strong>{{ translate('Discount') }} : </strong>
                    <strong
                        id="set-discount-amount">{{ $discount }}</strong>
                </div>
            @endif
        <!-- Product panels-->
            {{--<div style="margin-left: -1%" class="sharethis-inline-share-buttons"></div>--}}
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12">
            <?php
            $cart = false;
            if (session()->has('cart')) {
                foreach (session()->get('cart') as $key => $cartItem) {
                    if (is_array($cartItem) && $cartItem['id'] == $product['id']) {
                        $cart = $cartItem;
                    }
                }
            }

            ?>
            <h2>{{translate('description')}}</h2>
            <div class="d-block text-break text-dark __descripiton-txt __not-first-hidden">
                <div>
                    {!! $product->description !!}
                </div>
                <div class="show-more text-info text-right">
                    <span>
                        {{translate('see more')}}
                    </span>
                </div>
            </div>
            <form id="add-to-cart-form" class="mb-2">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                @foreach (json_decode($product->choice_options) as $key => $choice)

                    <div class="h3 p-0 pt-2 text-break">{{ $choice->title }}
                    </div>

                    <div class="d-flex justify-content-left flex-wrap">
                        @foreach ($choice->options as $key => $option)
                            <input class="btn-check" type="radio"
                                   id="{{ $choice->name }}-{{ $option }}"
                                   name="{{ $choice->name }}" value="{{ $option }}"
                                   @if($key == 0) checked @endif autocomplete="off">
                            <label class="btn btn-sm check-label mx-1 choice-input"
                                   for="{{ $choice->name }}-{{ $option }}">{{ $option }}</label>
                        @endforeach
                    </div>
            @endforeach

            <!-- Quantity + Add to cart -->
                <div class="d-flex justify-content-between">
                    <div class="product-description-label mt-2 text-dark h3">{{translate('Quantity')}}:</div>
                    <div class="product-quantity d-flex align-items-center">
                        <div class="input-group input-group--style-2 pr-3"
                             style="width: 160px;">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark" type="button"
                                        data-type="minus" data-field="quantity"
                                        disabled="disabled" style="padding: 10px">
                                        <i class="tio-remove  font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="hidden" id="check_max_qty" value="{{ $product['total_stock'] }}">
                            <input type="text" name="quantity"
                                   class="form-control input-number text-center cart-qty-field"
                                   placeholder="{{ translate('1') }}" value="1" min="1" max="100">
                            <span class="input-group-btn">
                                <button class="btn btn-number text-dark" type="button" data-type="plus"
                                        data-field="quantity" style="padding: 10px">
                                        <i class="tio-add  font-weight-bold"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row no-gutters mt-2 text-dark" id="chosen_price_div">
                    <div class="col-2">
                        <div class="product-description-label">{{translate('Total Price')}}:</div>
                    </div>
                    <div class="col-10">
                        <div class="product-price">
                            <strong id="chosen_price"></strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-2">
                    <button class="btn btn-primary"
                            onclick="addToCart()"
                            type="button"
                            style="width:37%; height: 45px">
                        <i class="tio-shopping-cart"></i>
                        {{translate('add')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    cartQuantityInitialize();
    getVariantPrice();
    $('#add-to-cart-form input').on('change', function () {
        getVariantPrice();
    });
</script>


<script>
    $('.show-more span').on('click', function(){
        $('.__descripiton-txt').toggleClass('__not-first-hidden')
        if($(this).hasClass('active')) {
            $('.show-more span').text('{{translate('See More')}}')
            $(this).removeClass('active')
        }else {
            $('.show-more span').text('{{translate('See Less')}}')
            $(this).addClass('active')
        }
    })
</script>

<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <i class="tio-clear"></i>
</button>
<div class="coupon__details">
    <div class="coupon__details-left">
        <div class="text-center">
            <h6 class="title" id="title">{{ $coupon->title }}</h6>
            <h6 class="subtitle">{{translate('code')}} : <span id="coupon_code">{{ $coupon->code }}</span></h6>
            <div class="text-capitalize">
                <span>{{translate(str_replace('_',' ',$coupon->coupon_type))}}</span>
            </div>
        </div>
        <div class="coupon-info">
            <div class="coupon-info-item">
                <span>{{translate('minimum_purchase')}} :</span>
                <strong id="min_purchase">{{Helpers::set_symbol($coupon->min_purchase)}}</strong>
            </div>
            @if($coupon->coupon_type != 'free_delivery' && $coupon->discount_type == 'percent')
            <div class="coupon-info-item" id="">
                <span>{{translate('maximum_discount')}} : </span>
                <strong id="max_discount">{{Helpers::set_symbol($coupon->max_discount)}}</strong>
            </div>
            @endif
            <div class="coupon-info-item">
                <span>{{translate('start_date')}} : </span>
                <span id="start_date">{{ \Carbon\Carbon::parse($coupon->start_date)->format('dS M Y') }}</span>
            </div>
            <div class="coupon-info-item">
                <span>{{translate('expire_date')}} : </span>
                <span id="expire_date">{{ \Carbon\Carbon::parse($coupon->expire_date)->format('dS M Y') }}</span>
            </div>
        </div>
    </div>
    <div class="coupon__details-right">
        <div class="coupon">
            @if($coupon->coupon_type == 'free_delivery')
                <img src="{{ asset('public/assets/admin/img/delivery/free-delivery.png') }}" alt="Free delivery" width="100">
            @else
                <div class="d-flex">
                    <h4 id="discount">
                        {{$coupon->discount_type=='amount'?(Helpers::set_symbol($coupon->discount)):$coupon->discount.'%'}}
                    </h4>
                </div>

                <span>{{translate('off')}}</span>
            @endif
        </div>
    </div>
</div>

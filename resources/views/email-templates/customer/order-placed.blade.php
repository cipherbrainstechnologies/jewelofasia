<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap');

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            line-height: 21px;
            color: #737883;
            background: #f7fbff;
            padding: 0;
            display: flex;align-items: center;justify-content: center;
            min-height: 100vh;
        }
        h1,h2,h3,h4,h5,h6 {
            color: #334257;
            margin: 0;
        }
        * {
            box-sizing: border-box
        }

        :root {
            --base: #006161
        }

        .main-table {
            width: 500px;
            background: #FFFFFF;
            margin: 0 auto;
            padding: 40px;
        }
        .main-table-td {
        }
        img {
            max-width: 100%;
        }
        .cmn-btn{
            background: var(--base);
            color: #fff;
            padding: 8px 20px;
            display: inline-block;
            text-decoration: none;
        }
        .mb-1 {
            margin-bottom: 5px;
        }
        .mb-2 {
            margin-bottom: 10px;
        }
        .mb-3 {
            margin-bottom: 15px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        .mb-5 {
            margin-bottom: 25px;
        }
        hr {
            border-color : rgba(0, 170, 109, 0.3);
            margin: 16px 0
        }
        .border-top {
            border-top: 1px solid rgba(0, 170, 109, 0.3);
            padding: 15px 0 10px;
            display: block;
        }
        .d-block {
            display: block;
        }
        .privacy {
            display: flex;
            align-items: center;
            justify-content: center;

        }
        .privacy a {
            text-decoration: none;
            color: #334257;
            position: relative;
        }
        .privacy a:not(:last-child)::after {
            content:'';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #334257;
            display: inline-block;
            margin: 0 15px
        }
        .social {
            margin: 15px 0 8px;
            display: block;
        }
        .copyright{
            text-align: center;
            display: block;
        }
        div {
            display: block;
        }
        .text-center {
            text-align: center;
        }
        .text-base {
            color: var(--base);
font-weight: 700
        }
        .font-medium {
            font-family: 500;
        }
        .font-bold {
            font-family: 700;
        }
        a {
            text-decoration: none;
        }
        .bg-section {
            background: #E3F5F1;
        }
        .p-10 {
            padding: 10px;
        }
        .mt-0{
            margin-top: 0;
        }
        .w-100 {
            width: 100%;
        }
        .order-table {
            padding: 10px;
            background: #fff;
        }
        .order-table tr td {
            vertical-align: top
        }
        .order-table .subtitle {
            margin: 0;
            margin-bottom: 10px;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .bg-section-2 {
            background: #F8F9FB;
        }
        .p-1 {
            padding: 5px;
        }
        .p-2 {
            padding: 10px;
        }
        .px-3 {
            padding-inline: 15px
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .m-0 {
            margin: 0;
        }
        .text-base {
            color: var(--base);
font-weight: 700
        }
        del {
            opacity: .5;
        }
        .total-amount{
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }
    </style>

</head>


<body>

@php($logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)
@php($business_name=\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value)
@php($footer=\App\Model\BusinessSetting::where(['key'=>'footer_text'])->first()->value)
<?php
$socialMediaList = \App\Model\SocialMedia::active()->get();
$platforms = ['facebook', 'pinterest', 'linkedin', 'instagram', 'twitter'];
$socialMediaLinks = [];

foreach ($socialMediaList as $social) {
    $social_name = $social['name'];
    $link = $social['link'];

    if (in_array($social_name, $platforms)) {
        $socialMediaLinks[$social_name] = $link;
    }
}
?>

<table class="main-table">
    <tbody>
        <tr>
            <td class="main-table-td">
                <h2 class="mb-3">{{ translate('Your Order ') }} #{{ $order->id }} {{ translate(' has been placed successfully') }}!</h2>
                <div class="mb-1">{{ translate('Hi ') }} {{ $order->customer ? $order->customer->f_name. ' '. $order->customer->l_name: '' }},</div>
                <div class="mb-4">{{ translate('Your order from') }} <a href="" class="text-base font-medium">{{ $business_name }}</a> {{ translate(' has been placed') }}</div>
{{--                <span class="d-block text-center mb-3">--}}
{{--                    <a href="" class="cmn-btn">Track Order</a>--}}
{{--                </span>--}}
                <table class="bg-section p-10 w-100">
                    <tbody>
                        <tr>
                            <td class="p-10">
                                <span class="d-block text-center">
                                    <img style="width: 125px" class="mb-" onerror="this.src='{{asset('/public/assets/admin/img//logo/main-logo.png')}}'"
                                         src="{{ asset('storage/app/public/restaurant/' . $logo) }}" alt="public/img">
                                    <h3 class="mb-3 mt-0">{{ translate('Order Info') }}</h3>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table class="order-table w-100">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h3 class="subtitle">{{ translate('Order Summary') }}</h3>
                                                <span class="d-block">{{ translate('Order') }} # {{$order->id}}</span>
                                                <span class="d-block">{{ $order->created_at }}</span>
                                            </td>
                                            @if($order->is_guest == 0 && isset($order->delivery_address))
                                                <td style="max-width:130px">
                                                    <h3 class="subtitle">{{ translate('Delivery Address') }}</h3>
                                                    <span class="d-block">{{ $order->delivery_address->contact_person_name }}</span>
                                                    <span class="d-block" >{{ $order->delivery_address->address }}</span>
                                                </td>
                                            @endif
                                        </tr>
                                        <td colspan="2">
                                            <table class="w-100">
                                                <thead class="bg-section-2">
                                                    <tr>
                                                        <th class="text-left p-1 px-3">Product</th>
                                                        <th class="text-right p-1 px-3">Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @php($sub_total=0)
                                                @php($total_tax=0)
                                                @php($total_dis_on_pro=0)
                                                @php($updated_total_tax=0)
                                                @php($total_product_discount=0)
                                                @php($vat_status = '')
                                                @foreach($order->details as $detail)
                                                    @if($detail->product_details !=null)
                                                        @php($product = json_decode($detail->product_details, true))

                                                        <tr>
                                                            <td class="text-left p-2 px-3">
                                                                {{$loop->iteration}}. {{$product['name']}}
                                                            </td>
                                                            <td class="text-right p-2 px-3">
                                                                <h4>
                                                                    @php($amount=$detail['price']*$detail['quantity'])
                                                                    {{ Helpers::set_symbol($amount) }}
                                                                </h4>
                                                            </td>
                                                        </tr>

                                                        @php($sub_total+=$amount)
                                                        @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                                                        @php($updated_total_tax+= $detail['vat_status'] === 'included' ? 0 : $detail['tax_amount']*$detail['quantity'])
                                                        @php($vat_status = $detail['vat_status'])
                                                        @php($total_product_discount += $detail['discount_on_product']*$detail['quantity'])
                                                    @endif
                                                @endforeach


                                                    <tr>
                                                        <td colspan="2">
                                                            <hr class="mt-0">
                                                            <table class="w-100">
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">{{ translate('Items Price') }}:</td>
                                                                    <td class="text-right p-1 px-3">{{ Helpers::set_symbol($sub_total) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">{{translate('Tax / VAT')}} {{ $vat_status == 'included' ? translate('(included)') : '' }}:</td>
                                                                    <td class="text-right p-1 px-3">{{ Helpers::set_symbol($total_tax) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">{{ translate('Subtotal') }}:</td>
                                                                    <td class="text-right p-1 px-3">{{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">Discount</td>
                                                                    <td class="text-right p-1 px-3">{{ Helpers::set_symbol($total_product_discount) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">{{ translate('Coupon Discount') }}:</td>
                                                                    <td class="text-right p-1 px-3">{{ Helpers::set_symbol($order['coupon_discount_amount']) }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">{{ translate('Delivery Fee') }}:</td>
                                                                    <td class="text-right p-1 px-3">
                                                                        @if($order['order_type']=='take_away')
                                                                            @php($del_c=0)
                                                                        @else
                                                                            @php($del_c=$order['delivery_charge'])
                                                                        @endif
                                                                        {{ Helpers::set_symbol($del_c) }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 40%"></td>
                                                                    <td class="p-1 px-3">
                                                                        <h4>{{ translate('Total') }}:</h4>
                                                                    </td>
                                                                    <td class="text-right p-1 px-3">
                                                                        <span class="text-base">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount'] - $total_product_discount) }}</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="mb-2">
                    {{ translate('Please contact us for any queries, we’re always happy to help.') }}
                </div>
                <div>
                    {{ translate('Thanks & Regards') }},
                </div>
                <div class="mb-4">
                    {{$business_name}}
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="privacy">
                    <a href="{{ route('pages.privacy-policy') }}">{{translate('Privacy Policy')}}</a><a href="{{ route('pages.about-us') }}">{{ translate('About Us') }}</a>
                </span>
                <span class="social" style="text-align:center">
                    <a href="{{ $socialMediaLinks['pinterest'] ?? '#' }}" style="margin: 0 5px;text-decoration:none">
                        <img src="{{asset('/public/assets/admin/img/img/pinterest.png')}}" alt="pinterest">
                    </a>
                    <a href="{{ $socialMediaLinks['instagram'] ?? '#' }}" style="margin: 0 5px;text-decoration:none">
                        <img src="{{asset('/public/assets/admin/img/img/instagram.png')}}" alt="instagram">
                    </a>
                    <a href="{{ $socialMediaLinks['facebook'] ?? '#' }}" style="margin: 0 5px;text-decoration:none">
                        <img src="{{asset('/public/assets/admin/img/img/facebook.png')}}" alt="facebook">
                    </a>
                    <a href="{{ $socialMediaLinks['linkedin'] ?? '#' }}" style="margin: 0 5px;text-decoration:none">
                        <img src="{{asset('/public/assets/admin/img/img/linkedin.png')}}" alt="linkedin">
                    </a>
                    <a href="{{ $socialMediaLinks['twitter'] ?? '#' }}" style="margin: 0 5px;text-decoration:none">
                        <img src="{{asset('/public/assets/admin/img/img/twitter.png')}}" alt="twitter">
                    </a>
                </span>
                <span class="copyright">
                    {{ $footer }}
                </span>
            </td>
        </tr>
    </tbody>
</table>


</body>
</html>

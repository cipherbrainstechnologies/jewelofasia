@extends('layouts.admin.app')

@section('title', translate('invoice'))

@section('content')
    
    <div class="content container-fluid">
        <div class="row justify-content-center" id="printableArea" style="color: #000;">
            <div class="col-md-12">
                <center>
                    <input type="button" class="btn btn--primary non-printable text-white" onclick="printDiv('printableArea')"
                           value="{{translate('Proceed, If thermal printer is ready.')}}"/>
                    <a href="{{url()->previous()}}" class="btn btn--danger non-printable text-white">{{ translate('Back') }}</a>
                </center>
                <hr class="non-printable">
            </div>
            <div class=" sds new" style="max-width:1130px !important;">
                <div class="pt-3">
                    
                </div>
                <div class="InvoiceHeader" style="padding-left:30px;padding-right:30px;">
                    <div class="InvoiceHeaderFirstRow mb-3">
                        <div class="row align-items-center">
                            <div class="col col-7 text-left" style="text-align:left;"><img src="{{asset('/public/assets/admin/img/Logo.png')}}" class="initial-38-2" alt="" style="width:auto;"></div>
                            <div class="col col-5"><div class="d-flex justify-content-start "><div class="CompanyAddress text-left"><strong>{{ $order->branch->name }}</strong><p>{{ $order->branch->address }}</p></div></div></div>
                        </div>
                    </div>
                    @php($address=\App\Model\CustomerAddress::find($order['delivery_address_id']))
                    <div class="InvoiceHeaderSecondRow" style="padding-left:30px;padding-right:30px;">
                        <div class="row align-items-start">
                            <div class="col col-3"></div>
                            <div class="col col-4"><div class="d-flex justify-content-start "><div class="ShippingAddress text-left"><p><strong>{{ translate('Ship To') }} : </strong><br/>{{ $order->delivery_address['contact_person_name'] ?? ''}}<br/>{{isset($address)?$address['address']:''}}<br/><strong>{{ translate('phone') }} : </strong>{{$order->delivery_address['contact_person_number'] ?? ''}}<br/></p></div></div></div>
                            <div class="col col-5"><div class="d-flex justify-content-start "><div class="CompanyAddress text-left"><p><strong>{{ translate('Invoice Date') }} : </strong>{{$order->created_at->format('m-d-Y')}}<br/><strong>{{ translate('Order ID') }} : </strong>{{$order['id']}}<br/><strong>{{ translate('Order Date') }} : </strong>{{date('d M Y h:i a',strtotime($order['created_at']))}}<br/><strong>{{ translate('Payment Method') }} </strong>: {{ucwords(str_replace('_', ' ', $order->payment_method))}}</strong></p></div></div></div>

                        </div>
                    </div>
                </div>
                <?php /*
                <div class="text-center pt-2 mb-3">
                    <h2  class="initial-38-3"></h2>
                    <h5 class="text-break initial-38-4">
                        
                    </h5>
                    <h5 class="initial-38-4 initial-38-3">
                        {{ translate('Phone') }} : {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}
                    </h5>
                    @if ($order->branch->gst_status)
                        <h5 class="initial-38-4 initial-38-3 fz-12px">
                            {{ translate('Gst No') }} : {{ $order->branch->gst_code }}
                        </h5>
                    @endif
                    {{-- <span class="text-center">Gst: {{$order->branch->gst_code}}</span> --}}
                </div>
                <span class="initial-38-5"><hr/></span>
                <div class="row mt-3">
                    <div class="col-6">
                        <h5>
                            <span class="font-light"> </span>
                        </h5>
                    </div>
                    <div class="col-6">
                        <h5>
                            <span class="font-light">
                            
                            </span>
                        </h5>
                    </div>
                    <div class="col-12">
                        @if($order->is_guest == 0)
                            @if(isset($order->customer))
                                <h5>
                                    <span class="font-light">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</span>
                                </h5>
                                <h5>
                                    <span class="font-light"></span>
                                </h5>
                                
                                <h5 class="text-break">
                                    {{ translate('address') }} :<span class="font-light">{</span>
                                </h5>
                            @endif
                        @else
                            @if($order->order_type == 'delivery')
                                @if(isset($order->delivery_address))
                                    <h5>
                                        {{ translate('Customer Name') }} :<span class="font-light">{{$order->delivery_address['contact_person_name']}}</span>
                                    </h5>
                                    <h5>
                                        {{ translate('Customer Name') }} :<span class="font-light">{{$order->delivery_address['contact_person_number']}}</span>
                                    </h5>
                                    <h5 class="text-break">
                                        {{ translate('address') }} :<span class="font-light">{{$order->delivery_address['address']}}</span>
                                    </h5>
                                @endif
                            @endif
                        @endif

                    </div>
                </div>
                <h5 class="text-uppercase"></h5>
                */ ?>
                <span class="initial-38-5"><hr style="border-color: #000;"/></span>
                <div class="table-outer" style="padding-left:30px;padding-right:30px;">
                    <table class="table table-bordered mt-3" style="color: #000;border:1px solid #000;border-collapse:collapse;">
                        <thead>
                        <tr style="border:1px solid #000;border-collapse:collapse;">
                            <th class="initial-38-7 border-top-0 border-bottom-0" style="width:52%;border:1px solid #000;border-collapse:collapse;">{{ translate('PRODUCT') }}</th>
                            <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12%;border:1px solid #000;border-collapse:collapse;">{{ translate('QTY') }}</th>
                            <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12%;border:1px solid #000;border-collapse:collapse;">{{ translate('UNIT PRICE') }}</th>
                            <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12%;border:1px solid #000;border-collapse:collapse;">{{ translate('DISCOUNT') }}</th>
                            <th class="initial-38-7 border-top-0 border-bottom-0" style="width:12%;border:1px solid #000;border-collapse:collapse;">{{ translate('PRICE') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @php($sub_total=0)
                        @php($total_tax=0)
                        @php($total_dis_on_pro=0)
                        @php($updated_total_tax=0)
                        @php($vat_status = '')
                        @foreach($order->details as $detail)

                            @if($detail->product_details !=null)
                                @php($product = json_decode($detail->product_details, true))
                                <tr style="border:1px solid #000;border-collapse:collapse;">
                                    <td class="" style="border:1px solid #000;border-collapse:collapse;">
                                        {{$product['name']}} <br>
                                        @if(count(json_decode($detail['variation'],true))>0)
                                            Variation : 
                                            @foreach(json_decode($detail['variation'],true)[0] ?? json_decode($detail['variation'],true) as $key1 =>$variation)
                                                <div class="font-size-sm">
                                                    <span class="text-capitalize">{{$key1}} :  </span>
                                                    <span class="font-weight-bold">{{$variation}} {{$key1=='price'?\App\CentralLogics\Helpers::currency_symbol():''}}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="" style="border:1px solid #000;border-collapse:collapse;">
                                        {{$detail['quantity']}}
                                    </td>
                                    <td class="" style="border:1px solid #000;border-collapse:collapse;">
                                        {{ Helpers::set_symbol($detail['price']) }}
                                    </td>
                                    <td class="" style="border:1px solid #000;border-collapse:collapse;">
                                        {{ Helpers::set_symbol($detail['discount_on_product']) }}
                                    </td>
                                    <td class="w-28p" style="border:1px solid #000;border-collapse:collapse;">
                                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                        {{ Helpers::set_symbol($amount) }}
                                    </td>
                                </tr>

                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                                @php($updated_total_tax+= $detail['vat_status'] === 'included' ? 0 : $detail['tax_amount']*$detail['quantity'])
                                @php($vat_status = $detail['vat_status'])

                            @endif

                        @endforeach
                        </tbody>
                    </table>
                    <div class="px-3">
                        <dl class="row text-right justify-content-center">
                            <dt class="col-6">{{ translate('Items Price') }}:</dt>
                            <dd class="col-6">{{ Helpers::set_symbol($sub_total) }}</dd>
                            <dt class="col-6">{{translate('Tax / VAT')}} {{ $vat_status == 'included' ? translate('(included)') : '' }}:</dt>
                            <dd class="col-6">{{ Helpers::set_symbol($total_tax) }}</dd>

                            <dt class="col-6">{{ translate('Subtotal') }}:</dt>
                            <dd class="col-6">
                                {{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</dd>
                            <dt class="col-6">{{ translate('Coupon Discount') }}:</dt>
                            <dd class="col-6">
                                - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>
                            @if($order['order_type'] == 'pos')
                                <dt class="col-6">{{translate('extra Discount')}}:</dt>
                                <dd class="col-6">
                                    - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
                            @endif
                            <dt class="col-6">{{ translate('Delivery Fee') }}:</dt>
                            <dd class="col-6">
                                @if($order['order_type']=='take_away')
                                    @php($del_c=0)
                                @else
                                    @php($del_c=$order['delivery_charge'])
                                @endif
                                {{ Helpers::set_symbol($del_c) }}
                                <hr style="border-color: #000;">
                            </dd>

                            <dt class="col-6 font-20px">{{ translate('Total') }}:</dt>
                            <dd class="col-6 font-20px">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd>
                        </dl>
                         <h5 class="text-center pt-3">
                          <div class="d-flex justify-content-start"><strong>{{ translate('Customer Note') }} : </strong><p>{{ $order->order_note}}</p></div>
                        </h5>
                        <span class="initial-38-5"><hr style="border-color: #000;"/></span>
                       
                        <h5 class="text-center pt-1">
                            <span class="d-block">"""{{ translate('THANK YOU') }}"""</span>
                        </h5>
                        <span class="initial-38-5"><hr style="border-color: #000;"/></span>
                        <span class="d-block text-center">{{ $footer_text->value }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endpush

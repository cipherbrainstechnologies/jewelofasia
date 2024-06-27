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
            <div class=" sds new" style="max-width:1130px !important;width:100%;padding:20px 30px;">
                <div class="pt-3">
                    
                </div>
                <div class="InvoiceHeader">
                    <div class="InvoiceHeaderFirstRow mb-3">
                        <div class="row align-items-center">
                            <div class="col col-12 " style="text-align:center;"><img src="{{asset('/public/assets/admin/img/Logo.png')}}" class="initial-38-2" alt="" style="width:auto;"></div>
                            <div class="col col-12 " style="text-align:center;"><div class="CompanyAddress text-center"><p style="margin:0;"><strong>{{ $order->branch->name }}</strong></p></div></div>
                            <div class="col col-12"><div class="d-flex justify-content-center text-center"><p>{{ $order->branch->address }}</p></div></div></div>
                        </div>
                    </div>
                    @php($address=\App\Model\CustomerAddress::find($order['delivery_address_id']))
                    <div class="InvoiceHeaderSecondRow">
                        <div class="row align-items-start">
                            <div class="col col-7"><div class="d-flex justify-content-start "><div class="ShippingAddress text-left"><p><strong>{{ translate('Ship To') }} : </strong><br/>{{ $order->delivery_address['contact_person_name'] ?? ''}}<br/>{{isset($address)?$address['address']:''}}<br/><strong>{{ translate('phone') }} : </strong>{{$order->delivery_address['contact_person_number'] ?? ''}}<br/></p></div></div></div>
                            <div class="col col-5">
                                <div class="outer" >
                                    <div class="d-flex justify-content-start align-items-start text-left">
                                        <div class="col col-5"><strong>{{ translate('Invoice Date') }} :</strong></div>
                                        <div class="col col-7">{{$order->created_at->format('m-d-Y')}}</div>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-start text-left">
                                        <div class="col col-5"><strong>{{ translate('Order ID') }} :</strong></div>
                                        <div class="col col-7">{{$order['id']}}</div>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-start text-left">
                                        <div class="col col-5"><strong>{{ translate('Order Date') }} :</strong></div>
                                        <div class="col col-7">{{date('d M Y h:i a',strtotime($order['created_at']))}}</div>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-start text-left">
                                        <div class="col col-5"><strong>{{ translate('Payment Method') }} :</strong></div>
                                        <div class="col col-7">{{ucwords(str_replace('_', ' ', $order->payment_method))}}</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <span class="initial-38-5"><hr style="border-color:transparent;margin-top: 2px;margin-bottom: 2px;"/></span>
                    <div class="table-outer">
                        <table class="table table-bordered mt-3" style="color: #000;border:1px solid #000;border-collapse:collapse;">
                            <thead>
                            <tr style="border:1px solid #000;border-collapse:collapse;">
                                <th class="initial-38-7 border-top-0 border-bottom-0" style="width:50%;border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{ translate('PRODUCT') }}</th>
                                <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12.5%;border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{ translate('QTY') }}</th>
                                <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12.5%;border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{ translate('UNIT PRICE') }}</th>
                                <th class="initial-38-6 border-top-0 border-bottom-0" style="width:12.5%;border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{ translate('DISCOUNT') }}</th>
                                <th class="initial-38-7 border-top-0 border-bottom-0" style="width:12.5%;border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{ translate('PRICE') }}</th>
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
                                        <td class="" style="border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">{{$product['name']}}</td>
                                        <td class="" style="border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">
                                            {{$detail['quantity']}}
                                        </td>
                                        <td class="" style="border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">
                                            {{ Helpers::set_symbol($detail['price']) }}
                                        </td>
                                        <td class="" style="border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">
                                            {{ Helpers::set_symbol($detail['discount_on_product']) }}
                                        </td>
                                        <td class="w-28p" style="border:1px solid #000;border-collapse:collapse;padding: 8px 10px;">
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
                                <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{ translate('Items Price') }}:</dt>
                                <dd class="col-6" style="padding-left:5px;padding-right:5px;">{{ Helpers::set_symbol($sub_total) }}</dd>
                                <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{translate('Tax / VAT')}} {{ $vat_status == 'included' ? translate('(included)') : '' }}:</dt>
                                <dd class="col-6" style="padding-left:5px;padding-right:5px;">{{ Helpers::set_symbol($total_tax) }}</dd>

                                <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{ translate('Subtotal') }}:</dt>
                                <dd class="col-6" style="padding-left:5px;padding-right:5px;">
                                    {{ Helpers::set_symbol($sub_total+$updated_total_tax) }}</dd>
                                <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{ translate('Coupon Discount') }}:</dt>
                                <dd class="col-6" style="padding-left:5px;padding-right:5px;">
                                    - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>
                                @if($order['order_type'] == 'pos')
                                    <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{translate('extra Discount')}}:</dt>
                                    <dd class="col-6" style="padding-left:5px;padding-right:5px;">
                                        - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
                                @endif
                                <dt class="col-6" style="padding-left:5px;padding-right:5px;">{{ translate('Delivery Fee') }}:</dt>
                                <dd class="col-6" style="padding-left:5px;padding-right:5px;">
                                    @if($order['order_type']=='take_away')
                                        @php($del_c=0)
                                    @else
                                        @php($del_c=$order['delivery_charge'])
                                    @endif
                                    {{ Helpers::set_symbol($del_c) }}
                                    <hr style="border-color: #000;margin: 5px 0;">
                                </dd>

                                <dt class="col-6 font-20px" style="padding-left:5px;padding-right:5px;">{{ translate('Total') }}:</dt>
                                <dd class="col-6 font-20px" style="padding-left:5px;padding-right:5px;">{{ Helpers::set_symbol($sub_total+$del_c+$updated_total_tax-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd>
                            </dl>
                             <h5 class="text-center pt-3">
                              <div class="d-flex justify-content-start"><strong>{{ translate('Customer Note') }} : </strong><p>{{ $order->order_note}}</p></div>
                            </h5>
                            <span class="initial-38-5"><hr style="border-color: #000;margin-top: 12px;margin-bottom: 7px;"/></span>
                           
                            <h5 class="text-center pt-1">
                                <span class="d-block">Thank You for the Order!! Eat Healthy, Stay Healthy</span>
                            </h5>
                            <span class="initial-38-5"><hr style="border-color: #000;margin-top: 7px;margin-bottom: 12px;"/></span>
                            <span class="d-block text-center">JOA Foods PTY LTD</span>
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

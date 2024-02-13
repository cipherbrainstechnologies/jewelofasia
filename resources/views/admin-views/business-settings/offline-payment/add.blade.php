@extends('layouts.admin.app')

@section('title', translate('offline Payment'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title mb-0 ml-2">
                    {{translate('Add Offline Payment Method')}}
                </span>
            </h2>
        </div>
        <!-- End Page Header -->

        <form action="{{route('admin.business-settings.web-app.third-party.offline-payment.store')}}" method="post">
            @csrf

            <div class="d-flex justify-content-end my-3">
                <div class="d-flex gap-2 justify-content-end align-items-center text-primary font-weight-bold" id="bkashInfoModalButton">
                    {{ translate('Section View') }}<i class="tio-info ml-1" data-toggle="tooltip" title="{{translate('Admin needs to add the payment information for any offline payment, which customers will use to pay.')}}"></i>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header flex-wrap gap-2">
                    <div class="justify-content-between align-items-center gy-2">
                        <h4 class="mb-0">
                            {{translate('Payment Information')}}
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn--primary" id="add-payment-method-field">
                            <i class="tio-add" ></i>
                            {{translate('Add New Field')}}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-end gap-3 mb-4">
                        <div class="flex-grow-1">
                            <label class="input-label">{{translate('Payment Method Name')}}</label>
                            <input type="text" maxlength="255" name="method_name" id="method_name" class="form-control"
                                   placeholder="{{ translate('ABC Company') }}" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-end gap-3 mb-4 flex-wrap field-row-payment">
                        <div class="flex-grow-1">
                            <div class="">
                                <label class="input-label">{{translate('Title')}} </label>
                                <input type="text" name="field_name[]" class="form-control" maxlength="255" placeholder="{{ translate('bank_Name') }}" required>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="">
                                <label class="input-label">{{translate('Data')}} </label>
                                <input type="text" name="field_data[]" class="form-control" maxlength="255" placeholder="{{ translate('ABC_Bank') }}" required>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 justify-content-end">
                            <button class="action-btn user-select-none opacity-0" disabled>
                                <i class="tio-delete-outlined"></i>
                            </button>
                        </div>
                    </div>

                    <div id="method-field"></div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3 mt-4">
                <div class="d-flex gap-2 justify-content-end text-primary align-items-center font-weight-bold" id="paymentInfoModalButton">
                    {{ translate('Section View') }} <i class="tio-info ml-1" data-toggle="tooltip" title="{{translate('Admin needs to set the required customer information, which needs to be provided to the customers before placing a booking through offline payment')}}"></i>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header flex-wrap gap-2">
                    <div class="justify-content-between align-items-center gy-2">
                        <h4 class="mb-0">
                            {{translate('Required Information from Customer')}}
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn--primary" id="add-payment-information-field">
                            <i class="tio-add"></i>
                            {{translate('add_New_Field')}}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-end gap-3 mb-4">
                        <div class="flex-grow-1">
                            <label class="input-label">{{translate('Payment Note')}}</label>
                            <textarea name="payment_note" class="form-control payment-note"  data-toggle="tooltip" title="Field is not editable"
                                      placeholder="{{ translate('payment_Note') }}" style="background-color: #e9ecef;" readonly ></textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-end gap-3 mb-4 flex-wrap field-row-customer" id="information-row--${count_info}">
                        <div class="flex-grow-1">
                            <div class="">
                                <label class="input-label">{{translate('Input Field Name')}} </label>
                                <input type="text" name="information_name[]" class="form-control" maxlength="255" placeholder="" id="information_name_${count_info}" required>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="">
                                <label class="input-label">{{translate('Input Field Placeholder/Hints')}} </label>
                                <input type="text" name="information_placeholder[]" class="form-control" maxlength="255" placeholder="" required>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <input class="custom-control mr-2" type="checkbox" name="information_required[]">
                                <label class="input-label mb-0">{{translate('Is Required')}}? </label>
                            </div>
                        </div>

                    </div>

                    <div id="information-field"></div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <button type="reset" id="reset" class="btn btn--reset">{{translate('reset')}}</button>
                <button type="sumbit"  class="btn btn--primary">{{translate('submit')}}</button>
            </div>
        </form>
    </div>

    <!-- Section View Modal -->

    <div class="modal fade" id="sectionViewModal" tabindex="-1" aria-labelledby="sectionViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="justify-content-end modal-header border-0">
                    <div data-dismiss="modal">
                        <i class="tio-clear"></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center flex-column gap-3 text-center">
                        <h3>{{translate('Offline Payment')}}</h3>
                        <img width="100" src="{{asset('public/assets/admin/img/offline_payment.png')}}" alt="">
                        <p class="text-muted">{{translate('This view is from the user app.')}} <br class="d-none d-sm-block"> {{translate('This is how customer will see in the app')}}</p>
                    </div>

                    <div class="rounded p-4 mt-3" id="offline_payment_top_part">
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <h4 id="payment_modal_method_name"><span></span></h4>
                            <div class="text-primary d-flex align-items-center gap-2">
                                {{translate('Pay on this account')}}
                                <i class="tio-checkmark-circle ml-1"></i>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2" id="methodNameDisplay">

                        </div>
                        <div class="d-flex flex-column gap-2" id="displayDataDiv">

                        </div>
                    </div>

                    <div class="rounded p-4 mt-3 mt-4" id="offline_payment_bottom_part">
                        <h2 class="text-center mb-4">{{translate('Amount')}} : xxx</h2>

                        <h4 class="mb-3">{{translate('Payment Info')}}</h4>
                        <div class="d-flex flex-column gap-3 mb-3" id="customer-info-display-div">

                        </div>
                        <div class="d-flex flex-column gap-3">
                            <textarea name="payment_note" id="payment_note" class="form-control"
                              readonly rows="3" placeholder="Note"></textarea>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button type="button" data-dismiss="modal" class="btn btn--secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')

<script>
    // Update the modal class based on the argument
    function openModal(contentArgument) {
        if (contentArgument === "bkashInfo") {
            $("#sectionViewModal #offline_payment_top_part").addClass("active");
            $("#sectionViewModal #offline_payment_bottom_part").removeClass("active");

            var methodName = $('#method_name').val();

            if (methodName !== '') {
                $('#payment_modal_method_name').text(methodName + ' ' + 'Info');
            }

            function extractPaymentData() {
                var data = [];

                $('.field-row-payment').each(function(index) {
                    var title = $(this).find('input[name="field_name[]"]').val();
                    var dataValue = $(this).find('input[name="field_data[]"]').val();
                    data.push({ title: title, data: dataValue });
                });

                return data;
            }

            var extractedData = extractPaymentData();


            function displayPaymentData() {
                var displayDiv = $('#displayDataDiv');
                var methodNameDisplay = $('#methodNameDisplay');
                methodNameDisplay.empty();
                displayDiv.empty();

                var paymentElement = $('<span>').text('Payment Method');
                var payementDataElement = $('<span>').html(methodName);

                var dataRow = $('<div>').addClass('d-flex gap-3 align-items-center mb-2');
                dataRow.append(paymentElement).append($('<span>').text(':')).append(payementDataElement);


                methodNameDisplay.append(dataRow);

                extractedData.forEach(function(item) {
                    var titleElement = $('<span>').text(item.title);
                    var dataElement = $('<span>').html(item.data);

                    var dataRow = $('<div>').addClass('d-flex gap-3 align-items-center');

                    if (item.title !== '') {
                        dataRow.append(titleElement).append($('<span>').text(':')).append(dataElement);
                        displayDiv.append(dataRow);
                    }

                });
            }
            displayPaymentData();

            //customer info
            function extractCustomerData() {
                var data = [];

                $('.field-row-customer').each(function(index) {
                    var fieldName = $(this).find('input[name="information_name[]"]').val();
                    var placeholder = $(this).find('input[name="information_placeholder[]"]').val();
                    var isRequired = $(this).find('input[name="information_required[]"]').prop('checked');
                    console.log(fieldName);
                    data.push({ fieldName: fieldName, placeholder: placeholder, isRequired: isRequired });
                });

                return data;
            }

            var extractedCustomerData = extractCustomerData();
            $('#customer-info-display-div').empty();

            // Loop through the extracted data and populate the display div
            $.each(extractedCustomerData, function(index, item) {
                var isRequiredAttribute = item.isRequired ? 'required' : '';
                var displayHtml = `
                        <input type="text" class="form-control" name="payment_by" readonly
                        id="payment_by" placeholder="${item.placeholder}"  ${isRequiredAttribute}>
                    `;
                $('#customer-info-display-div').append(displayHtml);
            });

        } else {
            $("#sectionViewModal #offline_payment_top_part").removeClass("active");
            $("#sectionViewModal #offline_payment_bottom_part").addClass("active");

            var methodName = $('#method_name').val();

            if (methodName !== '') {
                $('#payment_modal_method_name').text(methodName + ' ' + 'Info');
            }

            // $('.payment_modal_method_name').text(methodName);

            function extractPaymentData() {
                var data = [];

                $('.field-row-payment').each(function(index) {
                    console.log('modal')
                    var title = $(this).find('input[name="field_name[]"]').val();
                    var dataValue = $(this).find('input[name="field_data[]"]').val();
                    data.push({ title: title, data: dataValue });
                });

                return data;
            }

            var extractedData = extractPaymentData();


            function displayPaymentData() {
                var displayDiv = $('#displayDataDiv');
                var methodNameDisplay = $('#methodNameDisplay');
                methodNameDisplay.empty();
                displayDiv.empty();

                var paymentElement = $('<span>').text('Payment Method');
                var payementDataElement = $('<span>').html(methodName);

                var dataRow = $('<div>').addClass('d-flex gap-3 align-items-center mb-2');
                dataRow.append(paymentElement).append($('<span>').text(':')).append(payementDataElement);


                methodNameDisplay.append(dataRow);

                extractedData.forEach(function(item) {
                    var titleElement = $('<span>').text(item.title);
                    var dataElement = $('<span>').html(item.data);

                    var dataRow = $('<div>').addClass('d-flex gap-3 align-items-center');

                    if (item.title !== '') {
                        dataRow.append(titleElement).append($('<span>').text(':')).append(dataElement);
                        displayDiv.append(dataRow);
                    }

                });
            }
            displayPaymentData();

            //customer info
            function extractCustomerData() {
                var data = [];

                $('.field-row-customer').each(function(index) {
                    var fieldName = $(this).find('input[name="information_name[]"]').val();
                    var placeholder = $(this).find('input[name="information_placeholder[]"]').val();
                    var isRequired = $(this).find('input[name="information_required[]"]').prop('checked');
                    data.push({ fieldName: fieldName, placeholder: placeholder, isRequired: isRequired });
                });

                return data;
            }

            var extractedCustomerData = extractCustomerData();
            $('#customer-info-display-div').empty();

            // Loop through the extracted data and populate the display div
            $.each(extractedCustomerData, function(index, item) {
                var isRequiredAttribute = item.isRequired ? 'required' : '';
                var displayHtml = `
                        <input type="text" class="form-control" name="payment_by" readonly
                            id="payment_by" placeholder="${item.placeholder}"  ${isRequiredAttribute}>
                    `;
                $('#customer-info-display-div').append(displayHtml);
            });
        }

        // Open the modal
        $("#sectionViewModal").modal("show");
    }

    $(document).ready(function() {
        $("#bkashInfoModalButton").on('click', function() {
            console.log("something");
            var contentArgument = "bkashInfo";
            openModal(contentArgument);
        });
        $("#paymentInfoModalButton").on('click', function() {
            var contentArgument = "paymentInfo";
            openModal(contentArgument);
        });
    });
</script>

    <script>

        function delete_input_field(row_id) {
            //console.log(row_id);
            $( `#field-row--${row_id}` ).remove();
            count--;
        }

        function delete_information_input_field(row_id) {
            //console.log(row_id);
            $( `#information-row--${row_id}` ).remove();
            count_info--;
        }

        jQuery(document).ready(function ($) {
            count = 1;
            $('#add-payment-method-field').on('click', function (event) {
                if(count <= 15) {
                    event.preventDefault();

                    $('#method-field').append(
                        `<div class="d-flex align-items-end gap-3 mb-4 flex-wrap field-row-payment" id="field-row--${count}">
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">{{translate('Title')}} </label>
                                    <input type="text" name="field_name[]" class="form-control" maxlength="255" placeholder="{{ translate('Bank Name') }}" id="field_name_${count}" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">{{translate('Data')}} </label>
                                    <input type="text" name="field_data[]" class="form-control" maxlength="255" placeholder="{{ translate('ABC Bank') }}" required>
                                </div>
                            </div>
                            <div class="d-flex flex-grow-1 justify-content-center" data-toggle="tooltip" data-placement="top" title="{{translate('Remove the input field')}}">
                                <div class="action-btn btn--danger btn-outline-danger delete" onclick="delete_input_field(${count})">
                                    <i class="tio-delete-outlined"></i>
                                </div>
                            </div>
                        </div>`
                    );

                    count++;
                } else {
                    Swal.fire({
                        title: '{{translate('Reached maximum')}}',
                        confirmButtonText: '{{translate('ok')}}',
                    });
                }
            })


            count_info = 1;
            $('#add-payment-information-field').on('click', function (event) {
                if(count_info <= 15) {
                    event.preventDefault();

                    $('#information-field').append(
                        `<div class="d-flex align-items-end gap-3 mb-4 flex-wrap field-row-customer" id="information-row--${count_info}">
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">{{translate('Input Field Name')}} </label>
                                    <input type="text" name="information_name[]" class="form-control" maxlength="255" placeholder="" id="information_name_${count_info}" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">{{translate('Input Field Placeholder/Hints')}} </label>
                                    <input type="text" name="information_placeholder[]" class="form-control" maxlength="255" placeholder="" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <input class="custom-control mr-2" type="checkbox" name="information_required[]">
                                    <label class="input-label mb-0">{{translate('Is Required')}}? </label>
                                </div>
                            </div>
                            <div class="justify-content-center" data-toggle="tooltip" data-placement="top" title="{{translate('Remove the input field')}}">
                                <div class="action-btn btn--danger btn-outline-danger delete" onclick="delete_information_input_field(${count_info})">
                                     <i class="tio-delete-outlined"></i>
                                </div>
                            </div>
                        </div>`
                    );

                    count_info++;
                } else {
                    Swal.fire({
                        title: '{{translate('Reached maximum')}}',
                        confirmButtonText: '{{translate('ok')}}',
                    });
                }
            })

            $('#reset').on('click', function (event) {
                $('#method-field').html("");
                $('#method_name').val("");

                $('#information-field').html("");
                //$('#payment_note').val("");
                count=1;
                count_info=1;
            })
        });
    </script>

@endpush

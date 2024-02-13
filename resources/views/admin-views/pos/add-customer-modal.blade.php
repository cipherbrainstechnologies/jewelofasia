<div class="modal fade" id="add-customer" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Add new customer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.pos.customer.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('First name') }} <span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" name="f_name" class="form-control" value="" placeholder="{{ translate('First name') }}" required="">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('Last name') }} <span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" name="l_name" class="form-control" value="" placeholder="{{ translate('Last name') }}" required="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('Email') }}<span class="input-label-secondary text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="" placeholder="{{ translate('Ex : ex@example.com') }}" required="">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('Phone (With country code)') }}<span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="" placeholder="{{ translate('Phone') }}" required="">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{ translate('Reset') }}</button>
                        <button type="submit" id="submit_new_customer" class="btn btn--primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

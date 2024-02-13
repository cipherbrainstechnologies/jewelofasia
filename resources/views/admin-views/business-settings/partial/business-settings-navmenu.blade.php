
<div class="page-header">
    <h1 class="page-header-title">
        <span class="page-header-icon">
            <img src="{{asset('public/assets/admin/img/business-setup.png')}}" class="w--22" alt="">
        </span>
        <span>{{translate('Business  Setup')}}</span>
    </h1>
    <ul class="nav nav-tabs border-0 mb-3">
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/ecom-setup')?'active':''}}" href="{{route('admin.business-settings.store.ecom-setup')}}">
                {{translate('Business Settings')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/main-branch-setup')?'active':''}}" href="{{route('admin.business-settings.store.main-branch-setup')}}">
                {{translate('Main Branch Setup')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/timeSlot*')?'active':''}}" href="{{route('admin.business-settings.store.timeSlot.add-new')}}">
                {{translate('Delivery Time Slot')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/delivery-setup')?'active':''}}" href="{{route('admin.business-settings.store.delivery-setup')}}">
                {{translate('Delivery Fee Setup')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/product-setup')?'active':''}}" href="{{route('admin.business-settings.store.product-setup')}}">
                {{translate('Product Setup')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/cookies-setup')?'active':''}}" href="{{route('admin.business-settings.store.cookies-setup')}}">
                {{translate('Cookies Setup')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/otp-setup')?'active':''}}" href="{{route('admin.business-settings.store.otp-setup')}}">
                {{translate('OTP and Login Setup')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/customer-setup')?'active':''}}" href="{{route('admin.business-settings.store.customer-setup')}}">
                {{translate('customers')}}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/store/order-setup')?'active':''}}" href="{{route('admin.business-settings.store.order-setup')}}">
                {{translate('orders')}}
            </a>
        </li>
    </ul>
</div>

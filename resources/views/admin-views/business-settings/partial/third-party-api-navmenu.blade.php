
<h1 class="page-header-title">
    <span class="page-header-icon">
        <img src="{{asset('/public/assets/admin/img/third-party.png')}}" class="w--20" alt="">
    </span>
    <span>
        {{translate('Third Party')}}
    </span>
</h1>
<ul class="nav nav-tabs border-0 mb-3">
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/payment-method')?'active':''}}" href="{{route('admin.business-settings.web-app.payment-method')}}">
            {{translate('Payment Methods')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/social-media-login')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.social-media-login')}}">
            {{translate('Social Media Login')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/mail-config')?'active':''}}" href="{{route('admin.business-settings.web-app.mail-config')}}">
            {{translate('Mail Config')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/sms-module')?'active':''}}" href="{{route('admin.business-settings.web-app.sms-module')}}">
            {{translate('SMS Config')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/map-api-settings')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.map-api-settings')}}">
            {{translate('Google Map APIs')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/recaptcha*')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.recaptcha_index')}}">
            {{translate('Recaptcha')}}
        </a>
    </li>
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/fcm-index*')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.fcm-index')}}">--}}
{{--            {{translate('Push Notification')}}--}}
{{--        </a>--}}
{{--    </li>--}}
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/fcm-config*')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.fcm-config')}}">--}}
{{--            {{translate('Push Notification')}}--}}
{{--        </a>--}}
{{--    </li>--}}
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/chat-index*')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.chat-index')}}">
            {{translate('Social Media Chat')}}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{Request::is('admin/business-settings/web-app/third-party/firebase-otp-verification*')?'active':''}}" href="{{route('admin.business-settings.web-app.third-party.firebase-otp-verification')}}">
            {{translate('Firebase OTP Verification')}}
        </a>
    </li>
</ul>

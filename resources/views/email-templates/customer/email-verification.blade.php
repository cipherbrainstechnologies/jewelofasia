<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('Customer Email Verification') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            color: #334257;
            font-size: 13px;
            line-height: 1.5;
            display: flex;align-items: center;justify-content: center;
            min-height: 100vh;

        }

        table {
            border-collapse: collapse !important;
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
        .text-base {
            color: var(--base);
font-weight: 700
        }

    </style>
</head>

<body style="background-color: #e9ecef;padding:15px">

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

    <table style="width:100%;max-width:500px;margin:0 auto;text-align:center;background:#fff">
        <tr>
            <td style="padding:30px 30px 0">
                <img src="{{asset('/public/assets/admin/img/email-templates/forgot-password.png')}}" alt="forgot/png">
                <h3 style="font-size:17px;font-weight:500">{{ translate('Email Verification') }}</h3>
            </td>
        </tr>
        <tr>
            <td style="padding:0 30px 30px; text-align:left">
                <span style="display:block;margin-bottom:14px">{{ translate('Your Verification Code is') }}</span>
                <h2 style="font-size: 26px;margin: 0;letter-spacing:4px;text-align:center">{{$code ?? ''}}</h2>
                <br>
                <span class="border-top"></span>
                <span class="d-block" style="margin-bottom:14px">{{ translate('Please contact us for any queries, we’re always happy to help.') }}</span>
                <span class="d-block">{{ translate('Thanks & Regards') }},</span>
                <span class="d-block" style="margin-bottom:20px"> {{$business_name}}</span>

                <img style="width:120px;display:block;margin:10px auto" onerror="this.src='{{asset('/public/assets/admin/img//logo/main-logo.png')}}'"
                     src="{{ asset('storage/app/public/restaurant/' . $logo) }}" alt="public/img">
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
    </table>

</body>

</html>

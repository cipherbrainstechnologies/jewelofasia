<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        {{translate('Privacy policy')}}
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/custom.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">

    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/bootstrap.css">

</head>
<!-- Body-->
<body class="toolbar-enabled">

<nav class="navbar navbar-light bg-light justify-content-between">
    <h1 class="text-primary text-uppercase header-text">{{ Helpers::get_business_settings('restaurant_name') ?? translate('GroFresh') }}</h1>
</nav>

<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <div class="text-center">
                <h1>{{translate('Privacy policy')}}</h1>
            </div>
        </div>
        <section class="col-lg-12">
            {!! Helpers::get_business_settings('privacy_policy') ?? ''  !!}
        </section>
    </div>
</div>

</body>
</html>

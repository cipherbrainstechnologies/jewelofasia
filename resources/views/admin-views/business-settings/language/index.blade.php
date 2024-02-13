@extends('layouts.admin.app')

@section('title', translate('language'))


@section('content')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/lang.png')}}" class="w--24" alt="">
            </span>
            <span>
                {{translate('system settings')}}
            </span>
        </h1>
        <ul class="nav nav-tabs border-0 mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="{{route('admin.business-settings.web-app.system-setup.language.index')}}">
                    {{ translate('Language Setup') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.app_setting')}}">
                    {{ translate('App Settings') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.firebase_message_config_index')}}">
                    {{ translate('Firebase Configuration') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.business-settings.web-app.system-setup.db-index')}}">
                    {{ translate('Clean Database') }}
                </a>
            </li>
        </ul>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert--danger alert-danger mb-3" role="alert">
                <div class="d-flex">
                    <span class="alert--icon"><i class="tio-info"></i></span>
                    <strong class="text--title word-nobreak">{{translate('note')}} : </strong>
                    <div class="w-0 flex-grow align-self-center pl-2">
                        {{translate('changing_some_settings_will_take_time_to_show_effect_please_clear_session_or_wait_for_60_minutes_else_browse_from_incognito_mode')}}
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{route('admin.business-settings.web-app.system-setup.language.add-new')}}" method="post"
                        style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">{{translate('Language Name')}}</label>
                                    <input type="text" class="form-control" id="recipient-name" name="name" placeholder="{{translate('Ex : English')}}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-form-label">{{translate('Country Code')}} </label>
                                    <select class="form-control js-select2-custom w-100" id="code" name="code">
{{--                                        <option value="en">English(default)</option>--}}
                                        <option value="af">Afrikaans</option>
                                        <option value="sq">Albanian - shqip</option>
                                        <option value="am">Amharic - አማርኛ</option>
                                        <option value="ar">Arabic - العربية</option>
                                        <option value="an">Aragonese - aragonés</option>
                                        <option value="hy">Armenian - հայերեն</option>
                                        <option value="ast">Asturian - asturianu</option>
                                        <option value="az">Azerbaijani - azərbaycan dili</option>
                                        <option value="eu">Basque - euskara</option>
                                        <option value="be">Belarusian - беларуская</option>
                                        <option value="bn">Bengali - বাংলা</option>
                                        <option value="bs">Bosnian - bosanski</option>
                                        <option value="br">Breton - brezhoneg</option>
                                        <option value="bg">Bulgarian - български</option>
                                        <option value="ca">Catalan - català</option>
                                        <option value="ckb">Central Kurdish - کوردی (دەستنوسی عەرەبی)</option>
                                        <option value="zh">Chinese - 中文</option>
                                        <option value="zh-HK">Chinese (Hong Kong) - 中文（香港）</option>
                                        <option value="zh-CN">Chinese (Simplified) - 中文（简体）</option>
                                        <option value="zh-TW">Chinese (Traditional) - 中文（繁體）</option>
                                        <option value="co">Corsican</option>
                                        <option value="hr">Croatian - hrvatski</option>
                                        <option value="cs">Czech - čeština</option>
                                        <option value="da">Danish - dansk</option>
                                        <option value="nl">Dutch - Nederlands</option>
                                        <option value="en-AU">English (Australia)</option>
                                        <option value="en-CA">English (Canada)</option>
                                        <option value="en-IN">English (India)</option>
                                        <option value="en-NZ">English (New Zealand)</option>
                                        <option value="en-ZA">English (South Africa)</option>
                                        <option value="en-GB">English (United Kingdom)</option>
                                        <option value="en-US">English (United States)</option>
                                        <option value="eo">Esperanto - esperanto</option>
                                        <option value="et">Estonian - eesti</option>
                                        <option value="fo">Faroese - føroyskt</option>
                                        <option value="fil">Filipino</option>
                                        <option value="fi">Finnish - suomi</option>
                                        <option value="fr">French - français</option>
                                        <option value="fr-CA">French (Canada) - français (Canada)</option>
                                        <option value="fr-FR">French (France) - français (France)</option>
                                        <option value="fr-CH">French (Switzerland) - français (Suisse)</option>
                                        <option value="gl">Galician - galego</option>
                                        <option value="ka">Georgian - ქართული</option>
                                        <option value="de">German - Deutsch</option>
                                        <option value="de-AT">German (Austria) - Deutsch (Österreich)</option>
                                        <option value="de-DE">German (Germany) - Deutsch (Deutschland)</option>
                                        <option value="de-LI">German (Liechtenstein) - Deutsch (Liechtenstein)</option>
                                        <option value="de-CH">German (Switzerland) - Deutsch (Schweiz)</option>
                                        <option value="el">Greek - Ελληνικά</option>
                                        <option value="gn">Guarani</option>
                                        <option value="gu">Gujarati - ગુજરાતી</option>
                                        <option value="ha">Hausa</option>
                                        <option value="haw">Hawaiian - ʻŌlelo Hawaiʻi</option>
                                        <option value="he">Hebrew - עברית</option>
                                        <option value="hi">Hindi - हिन्दी</option>
                                        <option value="hu">Hungarian - magyar</option>
                                        <option value="is">Icelandic - íslenska</option>
                                        <option value="id">Indonesian - Indonesia</option>
                                        <option value="ia">Interlingua</option>
                                        <option value="ga">Irish - Gaeilge</option>
                                        <option value="it">Italian - italiano</option>
                                        <option value="it-IT">Italian (Italy) - italiano (Italia)</option>
                                        <option value="it-CH">Italian (Switzerland) - italiano (Svizzera)</option>
                                        <option value="ja">Japanese - 日本語</option>
                                        <option value="kn">Kannada - ಕನ್ನಡ</option>
                                        <option value="kk">Kazakh - қазақ тілі</option>
                                        <option value="km">Khmer - ខ្មែរ</option>
                                        <option value="ko">Korean - 한국어</option>
                                        <option value="ku">Kurdish - Kurdî</option>
                                        <option value="ky">Kyrgyz - кыргызча</option>
                                        <option value="lo">Lao - ລາວ</option>
                                        <option value="la">Latin</option>
                                        <option value="lv">Latvian - latviešu</option>
                                        <option value="ln">Lingala - lingála</option>
                                        <option value="lt">Lithuanian - lietuvių</option>
                                        <option value="mk">Macedonian - македонски</option>
                                        <option value="ms">Malay - Bahasa Melayu</option>
                                        <option value="ml">Malayalam - മലയാളം</option>
                                        <option value="mt">Maltese - Malti</option>
                                        <option value="mr">Marathi - मराठी</option>
                                        <option value="mn">Mongolian - монгол</option>
                                        <option value="ne">Nepali - नेपाली</option>
                                        <option value="no">Norwegian - norsk</option>
                                        <option value="nb">Norwegian Bokmål - norsk bokmål</option>
                                        <option value="nn">Norwegian Nynorsk - nynorsk</option>
                                        <option value="oc">Occitan</option>
                                        <option value="or">Oriya - ଓଡ଼ିଆ</option>
                                        <option value="om">Oromo - Oromoo</option>
                                        <option value="ps">Pashto - پښتو</option>
                                        <option value="fa">Persian - فارسی</option>
                                        <option value="pl">Polish - polski</option>
                                        <option value="pt">Portuguese - português</option>
                                        <option value="pt-BR">Portuguese (Brazil) - português (Brasil)</option>
                                        <option value="pt-PT">Portuguese (Portugal) - português (Portugal)</option>
                                        <option value="pa">Punjabi - ਪੰਜਾਬੀ</option>
                                        <option value="qu">Quechua</option>
                                        <option value="ro">Romanian - română</option>
                                        <option value="mo">Romanian (Moldova) - română (Moldova)</option>
                                        <option value="rm">Romansh - rumantsch</option>
                                        <option value="ru">Russian - русский</option>
                                        <option value="gd">Scottish Gaelic</option>
                                        <option value="sr">Serbian - српски</option>
                                        <option value="sh">Serbo-Croatian - Srpskohrvatski</option>
                                        <option value="sn">Shona - chiShona</option>
                                        <option value="sd">Sindhi</option>
                                        <option value="si">Sinhala - සිංහල</option>
                                        <option value="sk">Slovak - slovenčina</option>
                                        <option value="sl">Slovenian - slovenščina</option>
                                        <option value="so">Somali - Soomaali</option>
                                        <option value="st">Southern Sotho</option>
                                        <option value="es">Spanish - español</option>
                                        <option value="es-AR">Spanish (Argentina) - español (Argentina)</option>
                                        <option value="es-419">Spanish (Latin America) - español (Latinoamérica)</option>
                                        <option value="es-MX">Spanish (Mexico) - español (México)</option>
                                        <option value="es-ES">Spanish (Spain) - español (España)</option>
                                        <option value="es-US">Spanish (United States) - español (Estados Unidos)</option>
                                        <option value="su">Sundanese</option>
                                        <option value="sw">Swahili - Kiswahili</option>
                                        <option value="sv">Swedish - svenska</option>
                                        <option value="tg">Tajik - тоҷикӣ</option>
                                        <option value="ta">Tamil - தமிழ்</option>
                                        <option value="tt">Tatar</option>
                                        <option value="te">Telugu - తెలుగు</option>
                                        <option value="th">Thai - ไทย</option>
                                        <option value="ti">Tigrinya - ትግርኛ</option>
                                        <option value="to">Tongan - lea fakatonga</option>
                                        <option value="tr">Turkish - Türkçe</option>
                                        <option value="tk">Turkmen</option>
                                        <option value="tw">Twi</option>
                                        <option value="uk">Ukrainian - українська</option>
                                        <option value="ur">Urdu - اردو</option>
                                        <option value="ug">Uyghur</option>
                                        <option value="uz">Uzbek - o‘zbek</option>
                                        <option value="vi">Vietnamese - Tiếng Việt</option>
                                        <option value="wa">Walloon - wa</option>
                                        <option value="cy">Welsh - Cymraeg</option>
                                        <option value="fy">Western Frisian</option>
                                        <option value="xh">Xhosa</option>
                                        <option value="yi">Yiddish</option>
                                        <option value="yo">Yoruba - Èdè Yorùbá</option>
                                        <option value="zu">Zulu - isiZulu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="display table table-borderless table-hover min-w-980px"
                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('SL')}}</th>
                                <th class="border-0">{{translate('name')}}</th>
                                <th class="border-0">{{translate('Code')}}</th>
                                <th class="border-0 text-center">{{translate('status')}}</th>
                                <th class="border-0 text-center">{{translate('default')}} {{translate('status')}}</th>
                                <th class="border-0 w-260px text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($language = App\CentralLogics\Helpers::get_business_settings('language'))
                            @if(isset($language) && array_key_exists('code', $language[0]))
                                @foreach($language as $key =>$data)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$data['name']}}
                                        </td>
                                        <td>{{$data['code']}}</td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox"
                                                       onclick="updateStatus('{{route('admin.business-settings.web-app.system-setup.language.update-status')}}','{{$data['code']}}','{{$data['default']}}')"
                                                       class="toggle-switch-input" {{$data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label text mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox"
                                                       onclick="window.location.href ='{{route('admin.business-settings.web-app.system-setup.language.update-default-status', ['code'=>$data['code']])}}'"
                                                       class="toggle-switch-input" {{ ((array_key_exists('default', $data) && $data['default']==true) ? 'checked': ((array_key_exists('default', $data) && $data['default']==false) ? '' : 'disabled')) }}>
                                                <span class="toggle-switch-label text mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn--container justify-content-end">
                                                <a class="btn--primary-2 btn-outline-primary-2 btn-35px"
                                                    href="{{route('admin.business-settings.web-app.system-setup.language.translate',[$data['code']])}}">{{translate('translated data')}}</a>
                                                @if($data['code']!='en')
                                                    <a class="action-btn btn--primary btn-outline-primary" data-toggle="modal"
                                                        data-target="#lang-modal-update-{{$data['code']}}" href="javascript:void(0)"><i class="tio-edit"></i></a>
                                                    @if($data['default'] != true)
                                                        <button class="action-btn btn--danger btn-outline-danger" id="delete"
                                                                onclick="delete_language('{{ route('admin.business-settings.web-app.system-setup.language.delete',[$data['code']]) }}')"><i class="tio-delete-outlined"></i></button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($language) && array_key_exists('code', $language[0]))
        @foreach($language as $key =>$data)
            <div class="modal fade" id="lang-modal-update-{{$data['code']}}" tabindex="-1" role="dialog"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header pb-3 border-bottom">
                            <h5 class="modal-title"
                                id="exampleModalLabel">{{translate('update_language')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('admin.business-settings.web-app.system-setup.language.update')}}" method="post">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">{{translate('language')}} </label>
                                            <input type="text" class="form-control" value="{{$data['name']}}" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="message-text"
                                                   class="col-form-label">{{translate('country_code')}}</label>
                                            <select class="form-control" name="code" style="width: 100%">
                                                <option value="{{$data['code']}}">{{$data['code']}}</option>
                                            </select>
                                        </div>
                                    </div>
{{--                                        <div class="col-6">--}}
{{--                                            <div class="form-group">--}}
{{--                                                <label class="col-form-label">{{translate('direction')}}:</label>--}}
{{--                                                <select class="form-control" name="direction">--}}
{{--                                                    <option value="ltr" {{isset($data['direction'])?$data['direction']=='ltr'?'selected':'':''}}>LTR--}}
{{--                                                    </option>--}}
{{--                                                    <option value="rtl" {{isset($data['direction'])?$data['direction']=='rtl'?'selected':'':''}}>RTL--}}
{{--                                                    </option>--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                </div>
                                <input type="hidden" class="form-control" value="{{$data['status']}}" name="status">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn--reset"
                                        data-dismiss="modal">{{translate('close')}}</button>
                                <button type="submit"
                                        class="btn btn--primary">{{translate('update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
{{--    <script src="{{asset('public/assets/admin')}}/vendor/datatables/jquery.dataTables.min.js"></script>--}}
{{--    <script src="{{asset('public/assets/admin')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>--}}

    <!-- Page level custom scripts -->
    <script>
        $("#reset").on("click", function () {
            $('#code').val('af').trigger('change');
        });

        function updateStatus(route, code, default_status) {
            if(code == 'en') {
                Swal.fire({
                    title: '{{ translate("You can not change the status of English language") }}',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Okay',
                    denyButtonText: `cancel`,
                }).then((result) => {
                    location.reload();
                })
            } else if(default_status == 1) {
                Swal.fire({
                    title: '{{ translate("You can not change the status of default language") }}',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Okay',
                    denyButtonText: `cancel`,
                }).then((result) => {
                    location.reload();
                })
            } else {
                $.get({
                    url: route,
                    data: {
                        code: code,
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        }
    </script>

    <script>
        function delete_language(route) {
            Swal.fire({
                title: '{{translate('Are you sure to delete this')}}?',
                text: "{{translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: 'primary',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{translate('Yes, delete it')}}!'
            }).then((result) => {
                if (result.value) {
                    window.location.href = route;
                }
            })
        }
    </script>
@endpush

@extends('layouts.admin.app')

@section('title', translate('Messages'))
@push('css_or_js')
<link rel="stylesheet" href="{{asset('/public/assets/admin/css/lightbox.min.css')}}">
@endpush
@section('content')

<div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/messages.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{translate('Messages')}} <span class="badge badge-soft-primary ml-2" id="conversation_count"></span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="row g-0">
            <div class="col-md-4 col-xxl-3">
                <div class="card h-100 conv--sidebar--card rounded-right-0">
                    <!-- Body -->
                    <div class="card-header border-0 px-0 mx-20px">
                        <div class="conv-open-user w-100">
                            <img class="w-47px" src="{{asset('storage/app/public/admin')}}/{{auth('admin')->user()->image}}"
                                 onerror="this.src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'"
                                    alt="Image Description">
                            <div class="info">
                                <h6 class="subtitle mb-0">{{auth('admin')->user()->f_name}} {{auth('admin')->user()->l_name}}</h6>
                                <span>{{auth('admin')->user()->role->name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div class="input-group-overlay input-group-sm mb-3 mx-20px">
                            <input placeholder="{{ translate('Search user') }}"
                                class="cz-filter-search form-control form-control-sm appended-form-control"
                                type="text" id="search-conversation-user" autocomplete="off">
                        </div>
                        <div class="conv--sidebar"
                             id="conversation_sidebar">
                            @php($array=[])
                            @foreach($conversations as $conv)
                                @if(in_array($conv->user_id,$array)==false)
                                    @php(array_push($array,$conv->user_id))
                                    @php($user=\App\User::find($conv->user_id))
                                    @if(isset($user))
                                    @php($unchecked=\App\Model\Conversation::where(['user_id'=>$conv->user_id,'checked'=>0])->count())
                                        <div
                                            class="sidebar_primary_div customer-list {{$unchecked!=0?'conv-active':''}}"
                                            onclick="viewConvs('{{route('admin.message.view',[$conv->user_id])}}','customer-{{$conv->user_id}}')"
                                            id="customer-{{$conv->user_id}}">
                                            <div class="conv-open-user w-100">
                                                <img class="w-47px" src="{{asset('storage/app/public/profile/'.$user['image'])}}"
                                                onerror="this.src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'"
                                                alt="Image Description">
                                                <span class="status active"></span>
                                                <div class="info">
                                                    <h6 class="subtitle mb-0 sidebar_name chat-count">{{$user['f_name'].' '.$user['l_name']}}</h6>
                                                    <span>{{ translate('customer') }}</span>
                                                </div>
                                                <span class="{{$unchecked!=0?'badge badge-info':'badge badge-info'}}" id="counter-{{$conv->user_id}}">{{$unchecked!=0?$unchecked:''}}</span>
                                            </div>
                                        </div>
                                    @endif

                                @endif
                            @endforeach
                        </div>
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
            <div class="col-md-8 col-xxl-9 pl-0 view-conversion" id="view-conversation">
                <center class="h-100 d-flex justify-content-center align-items-center card __shadow rounded-left-0 py-5 py-md-0">
                    <img src="{{asset('/public/assets/admin/img/view-conv.png')}}" class="mw-100" alt="">
                    <div>
                        {{translate('Click from the customer list to view conversation')}}
                    </div>
                </center>
            </div>
        </div>
        <!-- End Row -->
    </div>
@endsection

@push('script_2')
    {{-- Search --}}


    <script>
        $("#search-conversation-user").on("keyup", function () {
            var input_value = this.value.toLowerCase().trim();

            let sidebar_primary_div = $(".sidebar_primary_div");
            let sidebar_name = $(".sidebar_name");

            for (i = 0; i < sidebar_primary_div.length; i++) {
                const text_value = sidebar_name[i].innerText;
                if (text_value.toLowerCase().indexOf(input_value) > -1) {
                    sidebar_primary_div[i].style.display = "";
                } else {
                    sidebar_primary_div[i].style.setProperty("display", "none", "important");
                }
            }
        });
    </script>

    <script>
        let current_selected_user = null;

        function viewConvs(url, id_to_active) {
            current_selected_user = id_to_active;     //for reloading conversation body

            //inactive selected user from sidebar
            var counter_element = $('#counter-'+ current_selected_user.slice(9));
            var customer_element = $('#'+current_selected_user);
            if(counter_element !== "undefined") {
                counter_element.empty();
                counter_element.removeClass("badge");
                counter_element.removeClass("badge-info");
            }
            if(customer_element !== "undefined") {
                customer_element.removeClass("conv-active");
            }


            $('.customer-list').removeClass('conv-active');
            $('#' + id_to_active).addClass('conv-active');
            $.get({
                url: url,
                success: function (data) {
                    $('#view-conversation').html(data.view);
                }
            });
        }

        function replyConvs(url) {
            var form = document.querySelector('#reply-form');
            var formdata = new FormData(form);

            if (!formdata.get('reply') && !formdata.get('images[]')) {
                toastr.error('{{translate("Reply message is required!")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                return "false";
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                type: 'POST',
                data: formdata,
                processData: false,
                contentType: false,
                success: function (data) {
                    toastr.success('{{translate('Message sent')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    $('#view-conversation').html(data.view);
                },
                error() {
                    toastr.error('{{translate("Reply message is required!")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function renderUserList() {
            $('#loading').show();
            $.ajax({
                url: "{{route('admin.message.get_conversations')}}",
                type: 'GET',
                cache: false,
                success: function (response) {
                    $('#loading').hide();
                    $("#conversation_sidebar").html(response.conversation_sidebar)

                },
                error: function (err) {
                    $('#loading').hide();
                }
            });
        }

    </script>

    {{-- fcm listener --}}
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script>
        @php($config=\App\CentralLogics\Helpers::get_business_settings('firebase_message_config'))
        firebase.initializeApp({
            apiKey: "{{ $config['apiKey'] ?? '' }}",
            authDomain: "{{ $config['authDomain'] ?? '' }}",
            projectId: "{{ $config['projectId'] ?? '' }}",
            storageBucket: "{{ $config['storageBucket'] ?? '' }}",
            messagingSenderId: "{{ $config['messagingSenderId'] ?? '' }}",
            appId: "{{ $config['appId'] ?? '' }}"
        });

        const messaging = firebase.messaging();

        //service worker registration
        if ('serviceWorker' in navigator) {
            var swRegistration = navigator.serviceWorker.register('{{ asset('firebase-messaging-sw.js') }}')
                .then(function (registration) {
                    getToken(registration);
                    {{-- toastr.success('{{translate("Service Worker successfully registered.")}}');--}}
                    //console.log('Registration successful, scope is:', registration.scope);
                    console.log('Service worker registration successful.');
                }).catch(function (err) {
                    {{-- toastr.error('{{translate("Service Worker Registration failed.")}}');--}}
                    //console.log('Service worker registration failed, error:', err);
                    console.log('Service worker registration failed.');
                });
        }

        function getToken(registration) {
            messaging.requestPermission()
                .then(function () {
                    let token = messaging.getToken({serviceWorkerRegistration: registration});
                    return token;
                })
                .then(function (token) {
                    update_fcm_token(token);    //update admin's fcm token
                })
                .catch((err) => {
                    //console.log('error:: ' + err);
                });
        }



        //Foreground State
        messaging.onMessage(payload => {
            renderUserList();
            if (current_selected_user != null && current_selected_user.slice(9) === payload.notification.body) {
                document.getElementById(current_selected_user).onclick();
            } else {
                toastr.info(payload.notification.title ? payload.notification.title : 'New message arrived.');
            }

        });

        //Background State
        // messaging.setBackgroundMessageHandler(function (payload) {
        //     return self.registration.showNotification(payload.data.title, {
        //         body: payload.data.body ? payload.data.body : '',
        //         icon: payload.data.icon ? payload.data.icon : ''
        //     });
        // });

        function update_fcm_token(token) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{route('admin.message.update_fcm_token')}}",
                data: {
                    fcm_token: token,
                },
                cache: false,
                success: function (data) {
                    // console.log(JSON.stringify(data));
                    // toastr.success(data.message);
                    console.log(data.message);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error('{{translate("FCM token updated failed")}}');
                }
            });
        }

        $(document).ready(function() {
            $('#con_value_set').this(val)
        });

    </script>

    <script>
        let count = $('.chat-count').length;
        $('#conversation_count').text(count);

    </script>


@endpush

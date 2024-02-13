<div class="card conv-open-card h-100 rounded-left-0">
    <!-- Header -->
    <div class="card-header m-2 rounded-0">
        <div class="conv-open-user w-100">
            <img class="avatar-img" src="{{asset('storage/app/public/profile/'.$user['image'])}}"
                 onerror="this.src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'"
                 alt="Image Description">
            <div class="info">
                <h6 class="subtitle">{{$user['f_name'].' '.$user['l_name']}}</h6>
                <span>{{$user['phone']}}</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="scroll-down px-3 my-3">
            @foreach($convs as $key=>$con)

                @if(($con->message!=null && $con->reply==null) || $con->is_reply == false)
                    <div class="d-flex">
                        <div class="opponents--text my-1">
                            @if(isset($con->message))
                                <p>{{$con->message}}</p>
                            @endif
                                <?php try {?>
                            @if($con->image != null && $con->image != "null" && count(json_decode($con->image, true)) > 0)
                                @php($image_array = json_decode($con->image, true))
                                <div class="__gap-2 w-100">
                                    @foreach($image_array as $image)
                                        <div>
                                            <a href="{{$image}}" data-lightbox >
                                                <img class="__img-120" src="{{$image}}" onerror="this.src='{{asset('public/assets/admin/img/900x400/img1.jpg')}}'" />
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <?php }catch (\Exception $e) {} ?>
                        </div>
                    </div>
                    <div>
{{--                        <p>{{$con->created_at->diffForHumans()}}</p>--}}
                        <p>{{date('Y-m-d h:i A', strtotime($con->created_at))}}</p>
                    </div>
                @endif
                @if(($con->reply!=null && $con->message==null) || $con->is_reply == true)
                    <div class="d-flex justify-content-end">
                        <div class="own--text my-1">
                            @if(isset($con->reply))
                                <p>{{$con->reply}}</p>
                            @endif
                                <?php try {?>
                            @if($con->image != null && $con->image != "null" && count(json_decode($con->image, true)) > 0)
                                @php($image_array = json_decode($con->image, true))
                                <div class="__gap-2">
                                    @foreach($image_array as $key=>$image)
                                        @php($image_url = $image)
                                        <div>
                                            <a href="{{asset('storage/app/public/conversation')}}/{{$image}}" data-lightbox >
                                                <img class="__img-120" src="{{asset('storage/app/public/conversation')}}/{{$image}}" /></a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <?php }catch (\Exception $e) {} ?>
                        </div>

                    </div>
                    <div class="d-flex justify-content-end">
{{--                        <p>{{$con->created_at->diffForHumans()}}</p>--}}
                        <p>{{date('Y-m-d h:i A', strtotime($con->created_at))}}</p>
                    </div>
                @endif
            @endforeach
            <div id="scroll-here"></div>
        </div>
        <form action="javascript:" method="post" class="mx-3 pb-3 position-relative" id="reply-form">
            @csrf
            <!-- Quill -->
            <div class="quill-custom_">
                <input class="form-control" name="reply" />
            </div>
            <!-- End Quill -->
            <!-- Footer -->
            <div id="accordion" class="d-flex justify-content-end conv-buttons">
                <button class="btn btn-primary mr-2 collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    {{\App\CentralLogics\translate('Upload')}} <i class="tio-upload"></i>
                </button>
                <button type="submit" onclick="replyConvs('{{route('admin.message.store',[$user->id])}}')"
                        class="btn btn-primary">{{\App\CentralLogics\translate('send')}} <i class="tio-send"></i>
                </button>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                 data-parent="#accordion">
                <div>
                    <div class="row g-2 mt-2" id="coba"></div>
                </div>
            </div>
            <!-- End Footer -->
        </form>
    </div>
    <!-- Body -->
</div>









<script>
    $(document).ready(function () {
        $('.scroll-down').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 0);
    });
</script>

{{-- Multi Image Picker --}}
<script>
    $('#collapseTwo').on('show.bs.collapse', function () {
        spartanMultiImagePicker();
    })

    $('#collapseTwo').on('hidden.bs.collapse', function () {
        document.querySelector("#coba").innerHTML = "";
    })


</script>
<script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>
<script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
<script>
    "use strict";
var lightbox = function (o) {
    var s = void 0,
        c = void 0,
        u = void 0,
        d = void 0,
        i = void 0,
        p = void 0,
        m = document,
        e = m.body,
        l = "fadeIn .3s",
        v = "fadeOut .3s",
        t = "scaleIn .3s",
        f = "scaleOut .3s",
        a = "lightbox-btn",
        n = "lightbox-gallery",
        b = "lightbox-trigger",
        g = "lightbox-active-item",
        y = function () {
            return e.classList.toggle("remove-scroll");
        },
        r = function (e) {
            if (
                ("A" === o.tagName && (e = e.getAttribute("href")),
                e.match(/\.(jpeg|jpg|gif|png)/))
            ) {
                var t = m.createElement("img");
                return (
                    (t.className = "lightbox-image"),
                    (t.src = e),
                    "A" === o.tagName &&
                        (t.alt = o.getAttribute("data-image-alt")),
                    t
                );
            }
            if (e.match(/(youtube|vimeo)/)) {
                var a = [];
                return (
                    e.match("youtube") &&
                        ((a.id = e
                            .split(/v\/|v=|youtu\.be\//)[1]
                            .split(/[?&]/)[0]),
                        (a.url = "youtube.com/embed/"),
                        (a.options = "?autoplay=1&rel=0")),
                    e.match("vimeo") &&
                        ((a.id = e
                            .split(/video\/|https:\/\/vimeo\.com\//)[1]
                            .split(/[?&]/)[0]),
                        (a.url = "player.vimeo.com/video/"),
                        (a.options = "?autoplay=1title=0&byline=0&portrait=0")),
                    (a.player = m.createElement("iframe")),
                    a.player.setAttribute("allowfullscreen", ""),
                    (a.player.className = "lightbox-video-player"),
                    (a.player.src = "https://" + a.url + a.id + a.options),
                    (a.wrapper = m.createElement("div")),
                    (a.wrapper.className = "lightbox-video-wrapper"),
                    a.wrapper.appendChild(a.player),
                    a.wrapper
                );
            }
            return m.querySelector(e).children[0].cloneNode(!0);
        },
        h = function (e) {
            var t = {
                next: e.parentElement.nextElementSibling,
                previous: e.parentElement.previousElementSibling,
            };
            for (var a in t)
                null !== t[a] && (t[a] = t[a].querySelector("[data-lightbox]"));
            return t;
        },
        x = function (e) {
            p.removeAttribute("style");
            var t = h(u)[e];
            if (null !== t)
                for (var a in ((i.style.animation = v),
                setTimeout(function () {
                    i.replaceChild(r(t), i.children[0]),
                        (i.style.animation = l);
                }, 200),
                u.classList.remove(g),
                t.classList.add(g),
                (u = t),
                c))
                    c.hasOwnProperty(a) && (c[a].disabled = !h(t)[a]);
        },
        E = function (e) {
            var t = e.target,
                a = e.keyCode,
                i = e.type;
            ((("click" == i && -1 !== [d, s].indexOf(t)) ||
                ("keyup" == i && 27 == a)) &&
                d.parentElement === o.parentElement &&
                (N("remove"),
                (d.style.animation = v),
                (p.style.animation = [f, v]),
                setTimeout(function () {
                    if ((y(), o.parentNode.removeChild(d), "A" === o.tagName)) {
                        u.classList.remove(g);
                        var e = m.querySelector("." + b);
                        e.classList.remove(b), e.focus();
                    }
                }, 200)),
            c) &&
                ((("click" == i && t == c.next) || ("keyup" == i && 39 == a)) &&
                    x("next"),
                (("click" == i && t == c.previous) ||
                    ("keyup" == i && 37 == a)) &&
                    x("previous"));
            if ("keydown" == i && 9 == a) {
                var l = ["[href]", "button", "input", "select", "textarea"];
                l = l.map(function (e) {
                    return e + ":not([disabled])";
                });
                var n = (l = d.querySelectorAll(l.toString()))[0],
                    r = l[l.length - 1];
                e.shiftKey
                    ? m.activeElement == n && (r.focus(), e.preventDefault())
                    : (m.activeElement == r && (n.focus(), e.preventDefault()),
                      r.addEventListener("blur", function () {
                          r.disabled && (n.focus(), e.preventDefault());
                      }));
            }
        },
        N = function (t) {
            ["click", "keyup", "keydown"].forEach(function (e) {
                "remove" !== t
                    ? m.addEventListener(e, function (e) {
                          return E(e);
                      })
                    : m.removeEventListener(e, function (e) {
                          return E(e);
                      });
            });
        };
    !(function () {
        if (
            ((s = m.createElement("button")).setAttribute(
                "aria-label",
                "Close"
            ),
            (s.className = a + " " + a + "-close"),
            ((i = m.createElement("div")).className = "lightbox-content"),
            i.appendChild(r(o)),
            ((p = i.cloneNode(!1)).className = "lightbox-wrapper"),
            (p.style.animation = [t, l]),
            p.appendChild(s),
            p.appendChild(i),
            ((d = i.cloneNode(!1)).className = "lightbox-container"),
            (d.style.animation = l),
            (d.onclick = function () {}),
            d.appendChild(p),
            "A" === o.tagName && "gallery" === o.getAttribute("data-lightbox"))
        )
            for (var e in (d.classList.add(n),
            (c = { previous: "", next: "" })))
                c.hasOwnProperty(e) &&
                    ((c[e] = s.cloneNode(!1)),
                    c[e].setAttribute("aria-label", e),
                    (c[e].className = a + " " + a + "-" + e),
                    (c[e].disabled = !h(o)[e]),
                    p.appendChild(c[e]));
        "A" === o.tagName &&
            (o.blur(), (u = o).classList.add(g), o.classList.add(b)),
            o.parentNode.insertBefore(d, o.nextSibling),
            y();
    })(),
        N();
};

Array.prototype.forEach.call(
    document.querySelectorAll("[data-lightbox]"),
    function (t) {
        t.addEventListener("click", function (e) {
            e.preventDefault(), new lightbox(t);
        });
    }
);

</script>
<script>
    function spartanMultiImagePicker() {
        document.querySelector("#coba").innerHTML = "";

        $("#coba").spartanMultiImagePicker({
            fieldName: 'images[]',
            maxCount: 4,
            rowHeight: '110px',
            groupClassName: 'col-6 col-sm-3 conv-upload-image',
            maxFileSize: '',
            {{--placeholderImage: {--}}
                {{--    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',--}}
                {{--    width: '100%',--}}
                {{--},--}}
            dropFileLabel: "Drop Here",
            onAddRow: function (index, file) {

            },
            onRenderedPreview: function (index) {

            },
            onRemoveRow: function (index) {

            },
            onExtensionErr: function (index, file) {
                toastr.error('{{translate("Please only input png or jpg type file")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            onSizeErr: function (index, file) {
                toastr.error('{{translate("File size too big")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        });
    }
</script>

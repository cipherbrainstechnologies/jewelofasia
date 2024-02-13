@foreach($delivery_men as $key=>$dm)
    <tr>
        <td>{{$key+1}}</td>
        <td>
            <div class="table--media">
                <img class="rounded-full"  onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="img">
                <div class="table--media-body">
                    <h5 class="title">
                        {{$dm['f_name'] . ' ' . $dm['l_name']}}
                    </h5>
                </div>
            </div>
        </td>
        <td>
            <h5 class="m-0">
                <a href="mailto:{{$dm['email']}}" class="text-hover">{{$dm['email']}}</a>
            </h5>
            <div>
                <a href="tel:{{$dm['phone']}}" class="text-hover">{{$dm['phone']}}</a>
            </div>
        </td>
        <td>
            <span class="badge badge-soft-info py-2 px-3 font-bold ml-3">
                700
            </span>
        </td>
        <td>
            <label class="toggle-switch toggle-switch-sm">
                <input type="checkbox" class="toggle-switch-input" class="toggle-switch-input">
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <!-- Dropdown -->
            <div class="btn--container justify-content-center">
                <a class="action-btn"
                    href="{{route('admin.delivery-man.edit',[$dm['id']])}}">
                <i class="tio-edit"></i>
            </a>
                <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                    onclick="form_alert('delivery-man-{{$dm['id']}}','{{translate('Want to remove this information ?')}}')">
                    <i class="tio-delete-outlined"></i>
                </a>
                <form action="{{route('admin.delivery-man.delete',[$dm['id']])}}"
                        method="post" id="delivery-man-{{$dm['id']}}">
                    @csrf @method('delete')
                </form>
            </div>
            <!-- End Dropdown -->
        </td>
    </tr>
@endforeach

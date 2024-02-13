
<div class="col-sm-6 col-lg-3">
    <a class="dashboard--card h-100" href="{{route('admin.orders.list',['pending'])}}">
        <h6 class="subtitle">{{translate('pending')}}</h6>
        <h2 class="title">
            {{$data['pending']}}
        </h2>
        <img src="{{asset('/public/assets/admin/img/dashboard/pending.png')}}" alt="" class="dashboard-icon">
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="dashboard--card h-100" href="{{route('admin.orders.list',['confirmed'])}}">
        <h6 class="subtitle">{{translate('confirmed')}}</h6>
        <h2 class="title">
            {{$data['confirmed']}}
        </h2>
        <img src="{{asset('/public/assets/admin/img/dashboard/confirmed.png')}}" alt="" class="dashboard-icon">
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="dashboard--card h-100" href="{{route('admin.orders.list',['processing'])}}">
        <h6 class="subtitle">{{translate('packaging')}}</h6>
        <h2 class="title">
            {{$data['processing']}}
        </h2>
        <img src="{{asset('/public/assets/admin/img/dashboard/packaging.png')}}" alt="" class="dashboard-icon">
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="dashboard--card h-100" href="{{route('admin.orders.list',['out_for_delivery'])}}">
        <h6 class="subtitle">{{translate('out_for_delivery')}}</h6>
        <h2 class="title">
            {{$data['out_for_delivery']}}
        </h2>
        <img src="{{asset('/public/assets/admin/img/dashboard/out-for-delivery.png')}}" alt="" class="dashboard-icon">
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="order--card h-100" href="{{route('admin.orders.list',['delivered'])}}">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                <img src="{{asset('public/assets/admin/img/delivery/1.png')}}" alt="dashboard" class="oder--card-icon">
                <span>{{translate('delivered')}}</span>
            </h6>
            <span class="card-title text-success">
                {{$data['delivered']}}
            </span>
        </div>
    </a>
</div>


<div class="col-sm-6 col-lg-3">
    <a class="order--card h-100" href="{{route('admin.orders.list',['canceled'])}}">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                <img src="{{asset('public/assets/admin/img/delivery/2.png')}}" alt="dashboard" class="oder--card-icon">
                <span>{{translate('Canceled')}}</span>
            </h6>
            <span class="card-title text-danger">
                {{$data['canceled']}}
            </span>
        </div>
    </a>
</div>
<!-- Static Cancel -->


<div class="col-sm-6 col-lg-3">
    <a class="order--card h-100" href="{{route('admin.orders.list',['returned'])}}">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                <img src="{{asset('public/assets/admin/img/delivery/3.png')}}" alt="dashboard" class="oder--card-icon">
                <span>{{translate('returned')}}</span>
            </h6>
            <span class="card-title text-warning">
                {{$data['returned']}}
            </span>
        </div>
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a class="order--card h-100" href="{{route('admin.orders.list',['failed'])}}">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                <img src="{{asset('public/assets/admin/img/delivery/4.png')}}" alt="dashboard" class="oder--card-icon">
                <span>{{translate('Failed to Delivered')}}</span>
            </h6>
            <span class="card-title text-danger">
                {{$data['failed']}}
            </span>
        </div>
    </a>
</div>

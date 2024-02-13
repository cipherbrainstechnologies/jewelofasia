<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Models\OfflinePaymentMethod;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfflinePaymentMethodController extends Controller
{
    public function __construct(
        private OfflinePaymentMethod $offline_payment_method,
        private Order $order
    ){}


    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        $query_param = [];
        $search = $request['search'];

        $methods = $this->offline_payment_method
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('method_name', 'LIKE', '%' . $key . '%');
                    }
                });
            })
            ->latest()
            ->paginate(Helpers::getPagination());

        return view('admin-views.business-settings.offline-payment.list', compact('methods', 'search'));
    }

    /**
     * @return Application|Factory|View
     */
    public function add(): Factory|View|Application
    {
        return view('admin-views.business-settings.offline-payment.add');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'method_name' => 'required',
            'field_name' => 'required|array',
            'field_data' => 'required|array',
            'information_name' => 'required|array',
            'information_placeholder' => 'required|array',
            'information_required' => '',
        ]);

        $method_fields = [];
        foreach ($request->field_name as $key=>$field_name) {
            $method_fields[] = [
                'field_name' => $request->field_name[$key],
                'field_data' => $request->field_data[$key],
            ];
        }

        $method_informations = [];
        foreach ($request->information_name as $key=>$field_name) {
            $method_informations[] = [
                'information_name' => $request->information_name[$key],
                'information_placeholder' => $request->information_placeholder[$key],
                'information_required' => isset($request['information_required']) && isset($request['information_required'][$key]) ? 1 : 0,
            ];
        }

        $method = $this->offline_payment_method;
        $method->method_name = $request->method_name;
        $method->method_fields = $method_fields;
        $method->payment_note = $request->payment_note;
        $method->method_informations = $method_informations;
        //dd($method);
        $method->save();

        Toastr::success(translate('successfully added'));
        return redirect()->route('admin.business-settings.web-app.third-party.offline-payment.list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $method = $this->offline_payment_method->find($request->id);
        $method->status = $request->status;
        $method->save();

        Toastr::success(translate('Status updated'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $method = $this->offline_payment_method->find($id);
        return view('admin-views.business-settings.offline-payment.edit', compact('method'));

    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'method_name' => 'required',
            'field_name' => 'required|array',
            'field_data' => 'required|array',
            'information_name' => 'required|array',
            'information_placeholder' => 'required|array',
            'information_required' => '',
        ]);


        $method_fields = [];
        foreach ($request->field_name as $key=>$field_name) {
            $method_fields[] = [
                'field_name' => $request->field_name[$key],
                'field_data' => $request->field_data[$key],
            ];
        }

        $method_informations = [];
        foreach ($request->information_name as $key=>$field_name) {
            $method_informations[] = [
                'information_name' => $request->information_name[$key],
                'information_placeholder' => $request->information_placeholder[$key],
                'information_required' => isset($request['information_required']) && isset($request['information_required'][$key]) ? 1 : 0,
            ];
        }

        $method = $this->offline_payment_method->find($id);
        $method->method_name = $request->method_name;
        $method->method_fields = $method_fields;
        $method->payment_note = $request->payment_note;
        $method->method_informations = $method_informations;
        $method->save();

        Toastr::success(translate('successfully updated'));
        return redirect()->route('admin.business-settings.web-app.third-party.offline-payment.list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $method = $this->offline_payment_method->find($request->id);
        $method->delete();

        Toastr::success(translate('successfully removed'));
        return back();
    }

    /**
     * @param Request $request
     * @param $status
     * @return Application|Factory|View
     */
    public function offline_payment_list(Request $request, $status): Factory|View|Application
    {
        $search = $request['search'];
        $status_mapping = [
            'pending' => 0,
            'denied' => 2,
        ];

        $status = $status_mapping[$status];

        $orders = $this->order->with(['offline_payment'])
            ->where(['payment_method' => 'offline_payment'])
            ->whereHas('offline_payment', function ($query) use($status){
                $query->where('status', $status);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                return $query->where(function ($query) use ($keys) {
                    foreach ($keys as $key) {
                        $query->where('id', 'LIKE', '%' . $key . '%')
                            ->orWhere('order_status', 'LIKE', "%{$key}%")
                            ->orWhere('payment_status', 'LIKE', "{$key}%");
                    }
                });
            })
            ->latest()
            ->paginate(Helpers::getPagination());

        return view('admin-views.order.offline-payment.list', compact('orders', 'search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quick_view_details(Request $request): JsonResponse
    {
        $order = $this->order->find($request->id);

        return response()->json([
            'view' => view('admin-views.order.offline-payment.details-quick-view', compact('order'))->render(),
        ]);
    }
}

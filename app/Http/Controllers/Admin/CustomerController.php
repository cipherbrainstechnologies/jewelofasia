<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Conversation;
use App\Model\Newsletter;
use App\Model\Order;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function __construct(
        private User $user,
        private Order $order,
        private Newsletter $newsletter,
        private BusinessSetting $business_setting,
        private Conversation $conversation
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function customer_list(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $customers = $this->user->with(['orders'])->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%")
                                ->orWhere('l_name', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%");
                        }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $customers = $this->user->with(['orders']);
        }
        $customers = $customers->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.customer.list', compact('customers','search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $customers=$this->user->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('admin-views.customer.partials._table',compact('customers'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function view(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        $customer = $this->user->find($id);
        if (isset($customer)) {
            $query_param = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_amount', 'like', "%{$value}%");
                        }
                });
                $query_param = ['search' => $request['search']];
            }else{
                $orders = $this->order->where(['user_id' => $id]);
            }
            $orders = $orders->latest()->paginate(Helpers::getPagination())->appends($query_param);

            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function subscribed_emails(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = $this->newsletter->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $newsletters = $this->newsletter;
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $customer = $this->user->find($request->id);
        //return $customer;
        if (Storage::disk('public')->exists('customer/' . $customer['image'])) {
            Storage::disk('public')->delete('customer/' . $customer['image']);
        }

        $conversations = $this->conversation->where('user_id', $request->id)->get();
        foreach ($conversations as $conversation){
            if ($conversation->checked == 0){
                $conversation->checked = 1;
                $conversation->save();
            }
        }

        $customer->delete();

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($customer['email'])) {
                $name = $customer->f_name. ' '. $customer->l_name;
                Mail::to($customer->email)->send(new \App\Mail\Customer\CustomerDelete($name));
            }
        } catch (\Exception $e) {
        }

        Toastr::success(translate('Customer removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $user = $this->user->find($request->id);
        $user->is_block = $request->status;
        $user->save();

        try {
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset($user['email'])) {
                Mail::to($user->email)->send(new \App\Mail\Customer\CustomerChangeStatus($user));
            }
        } catch (\Exception $e) {
        }

        Toastr::success(translate('Block status updated!'));
        return back();
    }


    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export_customer(Request $request): StreamedResponse|string
    {
        $storage = [];
        $query_param = [];
        $search = $request['search'];

        $customers = $this->user->when($request->has('search'), function ($query) use ($request) {
                $key = explode(' ', $request['search']);
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
                $query_param = ['search' => $request['search']];
            })
            ->get();

        foreach($customers as $customer){

            $storage[] = [
                'first_name' => $customer['f_name'],
                'last_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ];
        }
        return (new FastExcel($storage))->download('customers.xlsx');
    }
}

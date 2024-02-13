<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\Customer\OrderPlaced;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\CustomerAddress;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Traits\HelperTrait;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class POSController extends Controller
{
    use HelperTrait;
    public function __construct(
        private Branch $branch,
        private BusinessSetting $business_setting,
        private Category $category,
        private Order $order,
        private OrderDetail $order_detail,
        private Product $product,
        private User $user,
        private DeliveryMan $delivery_man
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $category = $request->query('category_id', 0);
        $categories = $this->category->where(['position' => 0])->active()->get();
        $keyword = $request->keyword;
        $key = explode(' ', $keyword);
        $users = $this->user->all();

        $products = $this->product->
            when($request->has('category_id') && $request['category_id'] != 0, function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [['id' => (string)$request['category_id']]]);
            })
            ->when($keyword, function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->active()->latest()->paginate(Helpers::getPagination());

        $branch = $this->branch->find(auth('branch')->id());
        return view('branch-views.pos.index', compact('categories', 'products', 'category', 'keyword', 'branch', 'users'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function quick_view(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->product->findOrFail($request->product_id);
        $discount = self::discount_calculation($product, $product['price']);

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos._quick-view-data', compact('product', 'discount'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function variant_price(Request $request): array
    {
        $product = $this->product->find($request->id);
        $str = '';
        $quantity = 0;
        $price = 0;

        foreach (json_decode($product->choice_options) as $key => $choice) {
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }

        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $discount = self::discount_calculation($product, $price);
                    $price = $price - $discount;
                }
            }
        } else {
            $price = $product->price;
            $discount = self::discount_calculation($product, $price);
            $price = $price - $discount;
        }

        return array('price' => Helpers::set_symbol(($price * $request->quantity)));
    }

    /**
     * @param $product
     * @param $price
     * @return float
     */
    public function discount_calculation($product, $price): float
    {
        $category_id = null;
        foreach (json_decode($product['category_ids'], true) as $cat) {
            if ($cat['position'] == 1) {
                $category_id = ($cat['id']);
            }
        }

        $category_discount = Helpers::category_discount_calculate($category_id, $price);
        $product_discount = Helpers::discount_calculate($product, $price);
        if ($category_discount >= $price){
            $discount = $product_discount;
        }else{
            $discount = max($category_discount, $product_discount);
        }
        return $discount;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_customers(Request $request): \Illuminate\Http\JsonResponse
    {
        $key = explode(' ', $request['q']);
        $data = DB::table('users')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->whereNotNull(['f_name', 'l_name', 'phone'])
            ->limit(8)
            ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        $data[] = (object)['id' => false, 'text' => translate('walk_in_customer')];

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update_tax(Request $request): \Illuminate\Http\RedirectResponse
    {
        if ($request->tax < 0) {
            Toastr::error(translate('Tax_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->tax > 100) {
            Toastr::error(translate('Tax_can_not_be_more_than_100_percent'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update_discount(Request $request): \Illuminate\Http\RedirectResponse
    {
        $total = session()->get('total');

        if ($request->type == 'percent' && $request->discount < 0) {
            Toastr::error(translate('Extra_discount_can_not_be_less_than_0_percent'));
            return back();
        } elseif ($request->type == 'percent' && $request->discount > 100) {
            Toastr::error(translate('Extra_discount_can_not_be_more_than_100_percent'));
            return back();
        }elseif ($request->type == 'amount' && $request->discount > $total) {
            Toastr::error(translate('Extra_discount_can_not_be_more_than_total_price'));
            return back();
        }

        $cart = $request->session()->get('cart', collect([]));
        $cart['extra_discount'] = $request->discount;
        $cart['extra_discount_type'] = $request->type;
        $request->session()->put('cart', $cart);

        Toastr::success(translate('Discount_applied'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateQuantity(Request $request): \Illuminate\Http\JsonResponse
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->product->find($request->id);

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;

        if ($product['total_stock'] < $request['quantity']){
            return response()->json([
                'data' => 0
            ]);
        }

        //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
        foreach (json_decode($product->choice_options) as $key => $choice) {
            $data[$choice->name] = $request[$choice->name];
            $variations[$choice->title] = $request[$choice->name];
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }
        $data['variations'] = $variations;
        $data['variant'] = $str;
        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) > 0) {
                foreach ($request->session()->get('cart') as $key => $cartItem) {
                    if (is_array($cartItem) && $cartItem['id'] == $request['id'] && $cartItem['variant'] == $str) {
                        return response()->json([
                            'data' => 1
                        ]);
                    }
                }

            }
        }
        //Check the string and decreases quantity for the stock
        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                }
            }
        } else {
            $price = $product->price;
        }

        $tax_on_product = Helpers::tax_calculate($product, $price);

        $discount = self::discount_calculation($product, $price);

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = $discount;
        $data['image'] = $product->image;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->push($data);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return response()->json([
            'data' => $data,
            'quantity' => $product->total_stock
        ]);
    }

    /**
     * @return Factory|View|Application
     */
    public function cart_items(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('branch-views.pos._cart');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emptyCart(Request $request): \Illuminate\Http\JsonResponse
    {
        session()->forget('cart');
        session()->forget('customer_id');
        session()->forget('address');
        session()->forget('order_type');
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFromCart(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return response()->json([], 200);
    }

    //order

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function order_list(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $query = $this->order->pos()->where(['branch_id' => auth('branch')->id()])->with(['customer', 'branch'])
                ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
                });

        $query_param = ['start_date' => $start_date,'end_date' => $end_date ];

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $orders = $query->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('branch-views.pos.order.list', compact('orders', 'search', 'start_date', 'end_date'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function order_details($id): View|Factory|RedirectResponse|Application
    {
        $order = $this->order->with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        $delivery_man = $this->delivery_man->where(['is_active'=>1])
            ->where(function($query) use ($order) {
                $query->where('branch_id', auth('branch')->id())
                    ->orWhere('branch_id', 0);
            })
            ->get();
        if (isset($order)) {
            return view('branch-views.order.order-view', compact('order', 'delivery_man'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function place_order(Request $request): RedirectResponse
    {
        if ($request->session()->has('cart')) {
            if (count($request->session()->get('cart')) < 1) {
                Toastr::error(translate('cart_empty_warning'));
                return back();
            }
        } else {
            Toastr::error(translate('cart_empty_warning'));
            return back();
        }

        $order_type = session()->has('order_type') ? session()->get('order_type') : 'take_away';

        $delivery_charge = 0;
        // store customer address for home delivery
        if ($order_type == 'home_delivery'){
            if (!session()->has('customer_id')){
                Toastr::error(translate('please select a customer'));
                return back();
            }

            if (!session()->has('address')){
                Toastr::error(translate('please select a delivery address'));
                return back();
            }

            $address_data = session()->get('address');
            $distance = $address_data['distance'] ?? 0;
            $delivery_type = Helpers::get_business_settings('delivery_management');
            if ($delivery_type['status'] == 1){
                $delivery_charge = Helpers::get_delivery_charge($distance);
            }else{
                $delivery_charge = Helpers::get_business_settings('delivery_charge');
            }

            $address = [
                'address_type' => 'Home',
                'contact_person_name' => $address_data['contact_person_name'],
                'contact_person_number' => $address_data['contact_person_number'],
                'address' => $address_data['address'],
                'floor' => $address_data['floor'],
                'road' => $address_data['road'],
                'house' => $address_data['house'],
                'longitude' => (string)$address_data['longitude'],
                'latitude' => (string)$address_data['latitude'],
                'user_id' => session()->get('customer_id'),
                'is_guest' => 0,
            ];
            $customer_address = CustomerAddress::create($address);
        }

        $cart = $request->session()->get('cart');
        $total_tax_amount = 0;
        $product_price = 0;
        $order_details = [];

        $order_id = 100000 + $this->order->all()->count() + 1;
        if ($this->order->find($order_id)) {
            $order_id = $this->order->orderBy('id', 'DESC')->first()->id + 1;
        }

        $order = $this->order;
        $order->id = $order_id;

        $order->user_id = session()->has('customer_id') ? session('customer_id') : null;
        $order->coupon_discount_title = $request->coupon_discount_title == 0 ? null : 'coupon_discount_title';
        $order->payment_status = $order_type == 'take_away' ? 'paid' : 'unpaid';
        $order->order_status = $order_type == 'take_away' ? 'delivered' : 'confirmed' ;
        $order->order_type = $order_type == 'take_away' ? 'pos' : 'delivery';
        $order->coupon_code = $request->coupon_code ?? null;
        $order->payment_method = $request->type;
        $order->transaction_reference = $request->transaction_reference ?? null;
        $order->delivery_charge = $delivery_charge;
        $order->delivery_address_id = $order_type == 'home_delivery' ? $customer_address->id : null;
        $order->delivery_date = Carbon::now()->format('Y-m-d');
        $order->order_note = null;
        $order->checked = 1;
        $order->created_at = now();
        $order->updated_at = now();

        foreach ($cart as $c) {
            if (is_array($c)) {
                $product = $this->product->find($c['id']);
                if(!empty($product['variations'])){
                    $type = $c['variant'];
                    foreach (json_decode($product['variations'], true) as $var) {
                        if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                            Toastr::error($var['type'] . ' ' . translate('is out of stock'));
                            return back();
                        }
                    }
                }else{
                    if(($product->total_stock - $c['quantity']) < 0) {
                        Toastr::error($product->name . ' ' . translate('is out of stock'));
                        return back();
                    }
                }
            }
        }

        foreach ($cart as $c) {
            if (is_array($c)) {

                $discount_on_product = 0;
                $product_subtotal = ($c['price']) * $c['quantity'];
                $discount_on_product += ($c['discount'] * $c['quantity']);

                $product = $this->product->find($c['id']);

                if ($product) {
                    $price = $c['price'];

                    $category_id = null;
                    foreach (json_decode($product['category_ids'], true) as $cat) {
                        if ($cat['position'] == 1){
                            $category_id = ($cat['id']);
                        }
                    }

                    $category_discount = Helpers::category_discount_calculate($category_id, $price);
                    $product_discount = Helpers::discount_calculate($product, $price);

                    if ($category_discount >= $price){
                        $discount = $product_discount;
                        $discount_type = 'discount_on_product';
                    }else{
                        $discount = max($category_discount, $product_discount);
                        $discount_type = $product_discount > $category_discount ? 'discount_on_product' : 'discount_on_category';
                    }

                    $product = Helpers::product_data_formatting($product);

                    $or_d = [
                        'product_id' => $c['id'],
                        'product_details' => $product,
                        'quantity' => $c['quantity'],
                        'price' => $price,
                        'tax_amount' => Helpers::tax_calculate($product, $price),
                        'discount_on_product' => $discount,
                        'discount_type' => $discount_type,
                        'variant' => json_encode($c['variant']),
                        'variation' => json_encode($c['variations']),
                        'vat_status' => Helpers::get_business_settings('product_vat_tax_status') === 'included' ? 'included' : 'excluded',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $total_tax_amount += $or_d['tax_amount'] * $c['quantity'];
                    $product_price += $product_subtotal - $discount_on_product;
                    $order_details[] = $or_d;
                }

                $var_store = [];
                if(!empty($product['variations'])){
                    $type = $c['variant'];
                    foreach ($product['variations'] as $var) {
//                        if ($type == $var['type']) {
//                            $var['stock'] -= $c['quantity'];
//                        }
                        if ($type == $var->type) {
                            $var->stock -= $c['quantity'];
                        }
                        $var_store[] = $var;
                    }
                }

                $this->product->where(['id' => $product['id']])->update([
                    'variations' => json_encode($var_store),
                    'total_stock' => $product['total_stock'] - $c['quantity'],
                    'popularity_count'=>$product['popularity_count']+1
                ]);
            }
        }

        $total_price = $product_price;
        if (isset($cart['extra_discount'])) {
            $extra_discount = $cart['extra_discount_type'] == 'percent' && $cart['extra_discount'] > 0 ? (($total_price * $cart['extra_discount']) / 100) : $cart['extra_discount'];
            $total_price -= $extra_discount;
        }
        $tax = isset($cart['tax']) ? $cart['tax'] : 0;
        $total_tax_amount = ($tax > 0) ? (($total_price * $tax) / 100) : $total_tax_amount;
        try {
            $order->extra_discount = $extra_discount ?? 0;
            $order->total_tax_amount = $total_tax_amount;
            $order->order_amount = $total_price + $total_tax_amount + $order->delivery_charge;

            $order->coupon_discount_amount = 0.00;
            $order->branch_id = auth('branch')->id();
            $order->save();

            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }

            $this->order_detail->insert($order_details);

            //send email
            if (session()->has('customer_id')){
                $emailServices = Helpers::get_business_settings('mail_config');
                $customer = $this->user->find($order->user_id);
                if (isset($emailServices['status']) && isset($customer->email) && $emailServices['status'] == 1) {
                    try {
                        Mail::to($customer->email)->send(new OrderPlaced($order->id));
                    }catch (\Exception $e) {
                        //
                    }
                }

                if ($order_type == 'home_delivery' && isset($customer)){
                    //push notification
                    $fcm_token = $customer->cm_firebase_token;
                    $language_code = $customer->language_code ?? 'en';

                    $message = Helpers::order_status_update_message('confirmed');

                    if ($language_code != 'en'){
                        $message = $this->translate_message($language_code, 'confirmed');
                    }

                    $order = $this->order->find($order_id);
                    $value = $this->dynamic_key_replaced_message(message: $message, type: 'order', order: $order);

                    try {
                        if ($value && $fcm_token != null) {
                            $data = [
                                'title' => 'Order',
                                'description' => $value,
                                'order_id' => $order_id,
                                'image' => '',
                                'type' => 'order'
                            ];
                            Helpers::send_push_notif_to_device($fcm_token, $data);
                        }
                    } catch (\Exception $e) {
                        //
                    }
                }
            }

            session()->forget('cart');
            session()->forget('customer_id');
            session()->forget('address');
            session()->forget('order_type');
            session(['last_order' => $order->id]);
            Toastr::success(translate('order_placed_successfully'));
            return back();
        } catch (\Exception $e) {
            info($e);
        }
        Toastr::warning(translate('failed_to_place_order'));
        return back();
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function generate_invoice($id): \Illuminate\Http\JsonResponse
    {
        $order = $this->order->where('id', $id)->first();
        $footer_text = $this->business_setting->where(['key' => 'footer_text'])->first();

        return response()->json([
            'success' => 1,
            'view' => view('branch-views.pos.order.invoice', compact('order'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store_keys(Request $request): \Illuminate\Http\JsonResponse
    {
        session()->put($request['key'], $request['value']);
        return response()->json('', 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function new_customer_store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users'
        ], [
            'f_name.required' => translate('first name is required'),
            'l_name.required' => translate('last name is required'),
            'email.required' => translate('email name is required'),
            'phone.required' => translate('phone name is required'),
            'email.unique' => translate('email must be unique'),
            'phone.unique' => translate('phone must be unique'),
        ]);

        $customer = $this->user;
        $customer->f_name = $request->f_name;
        $customer->l_name = $request->l_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->password = Hash::make('12345678');
        $customer->save();
        Toastr::success(translate('Customer added successfully!'));
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
    public function export_orders(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $query_param = [];
        $search = $request['search'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        $query = $this->order->pos()->where(['branch_id' => auth('branch')->id()])->with(['customer', 'branch'])
            ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            });

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $orders = $query->with('details')->orderBy('id', 'DESC')->get();

        $storage = [];
        foreach($orders as $order){
            $vat_status = $order->details[0] ? $order->details[0]->vat_status : '';
            if($vat_status == 'included'){
                $order_amount = $order['order_amount'] - $order['total_tax_amount'];
            }else{
                $order_amount = $order['order_amount'];
            }

            $branch = $order->branch ? $order->branch->name : '';
            $customer = $order->customer ? $order->customer->f_name .' '. $order->customer->l_name : 'Walking Customer';
            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $order_amount,
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount'=> $order['total_tax_amount'],
                'payment_method'=> $order['payment_method'],
                'order_type'=> $order['order_type'],
                'branch'=> $branch,
                'delivery_date'=>$order['delivery_date'],
            ];
        }
        return (new FastExcel($storage))->download('pos-orders.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addDeliveryInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 200);
        }

        $branch_id = auth('branch')->id();
        $branch = $this->branch->find($branch_id);
        $origin_lat = $branch['latitude'];
        $origin_lng = $branch['longitude'];
        $destination_lat = $request['latitude'];
        $destination_lng = $request['longitude'];

        $api_key = Helpers::get_business_settings('map_api_server_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin_lat . ',' . $origin_lng . '&destinations=' . $destination_lat . ',' . $destination_lng . '&key=' . $api_key);

        $data = json_decode($response, true);
        $distance_value = $data['rows'][0]['elements'][0]['distance']['value'];
        $distance = $distance_value/1000;


        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'Home',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'distance' => $distance,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('admin-views.pos._address', compact('address'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function get_distance(Request $request): mixed
    {
        $request->validate([
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required',
        ]);

        $api_key = Helpers::get_business_settings('map_api_server_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request['origin_lat'] . ',' . $request['origin_lng'] . '&destinations=' . $request['destination_lat'] . ',' . $request['destination_lng'] . '&key=' . $api_key);

        return $response->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function order_type_store(Request $request): JsonResponse
    {
        session()->put('order_type', $request['order_type']);
        return response()->json($request['order_type'], 200);
    }

}

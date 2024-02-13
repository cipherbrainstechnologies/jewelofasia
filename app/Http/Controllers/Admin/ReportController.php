<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Order;
use App\Model\OrderDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private Branch $branch,
        private BusinessSetting $business_setting,
        private Order $order,
        private OrderDetail $order_detail
    ){}

    /**
     * @return Factory|\Illuminate\Contracts\View\View|Application
     */
    public function order_index(): \Illuminate\Contracts\View\View|Factory|Application
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.order-index');
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function earning_index(): Factory|\Illuminate\Contracts\View\View|Application
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.earning-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function set_date(Request $request): \Illuminate\Http\RedirectResponse
    {
        $fromDate = Carbon::parse($request['from'])->startOfDay();
        $toDate = Carbon::parse($request['to'])->endOfDay();

        session()->put('from_date', $fromDate);
        session()->put('to_date', $toDate);
        return back();
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function sale_report(): \Illuminate\Contracts\View\View|Factory|Application
    {
        return view('admin-views.report.sale-report');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sale_filter(Request $request): \Illuminate\Http\JsonResponse
    {
        $branch_id = $request['branch_id'];
        $from = $to = null;
        if (!is_null($request->from) && !is_null($request->to))
        {
            $from = Carbon::parse($request->from)->format('Y-m-d');
            $to = Carbon::parse($request->to)->format('Y-m-d');
        }


        if ($branch_id == 'all') {
            $orders = $this->order->
                when((!is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                    //return $query->whereBetween('created_at', [$from, $to]);
                    return $query->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                })->pluck('id')->toArray();
        } else {
            $orders = $this->order->where(['branch_id' => $branch_id])
                ->when((!is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                    //return $query->whereBetween('created_at', [$from, $to]);
                    return $query->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                })->pluck('id')->toArray();
        }

        $data = [];
        $total_sold = 0;
        $total_qty = 0;

        foreach ($this->order_detail->whereIn('order_id', $orders)->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];

            $product = json_decode($detail->product_details, true);
            $images = $product['image'] != null ? (gettype($product['image'])!='array'?json_decode($product['image'],true):$product['image']) : [];
            $product_image = count($images) > 0 ? $images[0] : null;

            $data[] = [
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product_image' => $product_image,
                'order_id' => $detail['order_id'],
                'date' => $detail['created_at'],
                'price' => $ord_total,
                'quantity' => $detail['quantity'],
            ];

            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        return response()->json([
            'order_count' => count($data),
            'item_qty' => $total_qty,
            'order_sum' => \App\CentralLogics\Helpers::set_symbol($total_sold),
            'view' => view('admin-views.report.partials._table', compact('data'))->render(),
        ]);
    }

    /**
     * @return mixed
     */
    public function export_sale_report(): mixed
    {
        $data = session('export_sale_data');
        $pdf = PDF::loadView('admin-views.report.partials._report', compact('data'));
        return $pdf->download('sale_report_'.rand(00001,99999) . '.pdf');
    }


    /**
     * @param Request $request
     * @return Factory|\Illuminate\Contracts\View\View|Application
     */
    public function new_sale_report(Request $request): \Illuminate\Contracts\View\View|Factory|Application
    {
        $query_param = [];
        $branches = $this->branch->all();
        $branch_id = $request['branch_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        if ($branch_id == 'all') {
            $orders = $this->order->
            when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })->pluck('id')->toArray();

        } else {
            $orders = $this->order->where(['branch_id' => $branch_id])
                ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->pluck('id')->toArray();
        }
        $query_param = ['branch_id' => $branch_id, 'start_date' => $start_date,'end_date' => $end_date ];

        $order_details = $this->order_detail->withCount(['order'])->whereIn('order_id', $orders)->paginate(Helpers::getPagination())->appends($query_param);

        $data = [];
        $total_sold = 0;
        $total_qty = 0;
        foreach ($this->order_detail->whereIn('order_id', $orders)->get() as $detail) {
            $price = $detail['price'] - $detail['discount_on_product'];
            $ord_total = $price * $detail['quantity'];

            $product = json_decode($detail->product_details, true);

            $data[] = [
                'product_id' => $product['id'],
            ];
            $total_sold += $ord_total;
            $total_qty += $detail['quantity'];
        }

        $total_order = count($data);

        return view('admin-views.report.new-sale-report', compact( 'orders', 'total_order', 'total_sold', 'total_qty', 'order_details', 'branches', 'branch_id', 'start_date', 'end_date'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function expense_index(Request $request): Factory|\Illuminate\Contracts\View\View|Application
    {
        $search = $request['search'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
        $date_type = $request['date_type'] ?? 'this_year';
        $query_param = ['search' => $search, 'date_type' => $date_type, 'start_date' => $start_date, 'end_date' => $end_date];

        $expense_calculate = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query){
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            })
            ->when(($date_type == 'this_year'), function ($query) {
                return $query->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($date_type == 'custom_date' && !is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })
            ->latest()
            ->get();


        $total_expense = 0;
        $extra_discount = 0;
        $free_delivery = 0;
        $free_delivery_over_amount = 0;
        $coupon_discount = 0;
        if($expense_calculate){
            foreach ($expense_calculate as $calculate){
                $extra_discount += $calculate->extra_discount;
                $free_delivery_over_amount += $calculate->free_delivery_amount;
                if(isset($calculate->coupon->coupon_type) && $calculate->coupon->coupon_type == 'free_delivery'){
                    $free_delivery += $calculate->coupon_discount_amount;
                }else{
                    $coupon_discount += $calculate->coupon_discount_amount;
                }
            }
        }
        $free_delivery += $free_delivery_over_amount;
        $total_expense = $extra_discount + $free_delivery + $coupon_discount;

        $expense_transaction_chart = self::expense_transaction_chart_filter($request);

        $expense_transactions_table = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query){
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('coupon_code', 'like', "%{$search}%");
            })
            ->when(($date_type == 'this_year'), function ($query) {
                return $query->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($date_type == 'custom_date' && !is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                return $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($query_param);

        return view('admin-views.report.expense-report', compact('search', 'start_date', 'end_date', 'date_type', 'expense_transactions_table', 'total_expense', 'free_delivery', 'coupon_discount', 'extra_discount', 'expense_transaction_chart'));
    }

    /**
     * @param $request
     * @return array[]|void
     */
    public function expense_transaction_chart_filter($request)
    {
        $from = $request['start_date'];
        $to = $request['end_date'];
        $date_type = $request['date_type'] ?? 'this_year';

        if ($date_type == 'this_year') { //this year table
            $number = 12;
            $default_inc = 1;
            $current_start_year = date('Y-01-01');
            $current_end_year = date('Y-12-31');
            $from_year = Carbon::parse($from)->format('Y');

            $this_year = self::expense_transaction_same_year($request, $current_start_year, $current_end_year, $from_year, $number, $default_inc);
            return $this_year;

        } elseif ($date_type == 'this_month') { //this month table
            $current_month_start = date('Y-m-01');
            $current_month_end = date('Y-m-t');
            $inc = 1;
            $month = date('m');
            $number = date('d', strtotime($current_month_end));

            $this_month = self::expense_transaction_same_month($request, $current_month_start, $current_month_end, $month, $number, $inc);
            return $this_month;

        } elseif ($date_type == 'this_week') {
            $this_week = self::expense_transaction_this_week($request);
            return $this_week;

        } elseif ($date_type == 'custom_date' && !empty($from) && !empty($to)) {
            $start_date = Carbon::parse($from)->format('Y-m-d 00:00:00');
            $end_date = Carbon::parse($to)->format('Y-m-d 23:59:59');
            $from_year = Carbon::parse($from)->format('Y');
            $from_month = Carbon::parse($from)->format('m');
            $from_day = Carbon::parse($from)->format('d');
            $to_year = Carbon::parse($to)->format('Y');
            $to_month = Carbon::parse($to)->format('m');
            $to_day = Carbon::parse($to)->format('d');

            if ($from_year != $to_year) {
                $different_year = self::expense_transaction_different_year($request, $start_date, $end_date, $from_year, $to_year);
                return $different_year;

            } elseif ($from_month != $to_month) {
                $same_year = self::expense_transaction_same_year($request, $start_date, $end_date, $from_year, $to_month, $from_month);
                return $same_year;

            } elseif ($from_month == $to_month) {
                $same_month = self::expense_transaction_same_month($request, $start_date, $end_date, $from_month, $to_day, $from_day);
                return $same_month;
            }

        }
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $from_year
     * @param $number
     * @param $default_inc
     * @return array[]
     */
    public function expense_transaction_same_year($request, $start_date, $end_date, $from_year, $number, $default_inc): array
    {
        $orders = self::expense_chart_common_query($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year, MONTH(created_at) month')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%M')"))
            ->latest('created_at')
            ->get();

        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $month = date("F", strtotime("2023-$inc-01"));
            $discount_amount[$month . '-' . $from_year] = 0;
            foreach ($orders as $match) {
                if ($match['month'] == $inc) {
                    $discount_amount[$month . '-' . $from_year] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $month_date
     * @param $number
     * @param $default_inc
     * @return array[]
     */
    public function expense_transaction_same_month($request, $start_date, $end_date, $month_date, $number, $default_inc): array
    {
        $month = date("F", strtotime("2023-$month_date-01"));
        $orders = self::expense_chart_common_query($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%D')"))
            ->latest('created_at')->get();

        for ($inc = $default_inc; $inc <= $number; $inc++) {
            $day = Carbon::createFromFormat('j', $inc)->format('jS');
            $discount_amount[$day . '-' . $month] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $inc) {
                    $discount_amount[$day . '-' . $month] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @return array[]
     */
    public function expense_transaction_this_week($request): array
    {
        $start_date = Carbon::now()->startOfWeek();
        $end_date = Carbon::now()->endOfWeek();

        $number = 6;
        $period = CarbonPeriod::create(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek());
        $day_name = array();
        foreach ($period as $date) {
            $day_name[] = $date->format('l');
        }

        $orders = self::expense_chart_common_query($request)
            ->select(
                DB::raw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount'),
                DB::raw("(DATE_FORMAT(created_at, '%W')) as day")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%D')"))
            ->latest('created_at')->get();

        for ($inc = 0; $inc <= $number; $inc++) {
            $discount_amount[$day_name[$inc]] = 0;
            foreach ($orders as $match) {
                if ($match['day'] == $day_name[$inc]) {
                    $discount_amount[$day_name[$inc]] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @param $start_date
     * @param $end_date
     * @param $from_year
     * @param $to_year
     * @return array[]
     */
    public function expense_transaction_different_year($request, $start_date, $end_date, $from_year, $to_year): array
    {
        $orders = self::expense_chart_common_query($request)
            ->selectRaw('sum(coupon_discount_amount) as discount_amount, sum(extra_discount) as extra_discount, sum(free_delivery_amount) as free_delivery_amount,
                        YEAR(created_at) year')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y')"))
            ->latest('created_at')->get();

        for ($inc = $from_year; $inc <= $to_year; $inc++) {
            $discount_amount[$inc] = 0;
            foreach ($orders as $match) {
                if ($match['year'] == $inc) {
                    $discount_amount[$inc] = $match['discount_amount'] + $match['extra_discount'] + $match['free_delivery_amount'];
                }
            }
        }

        return array(
            'discount_amount' => $discount_amount,
        );
    }

    /**
     * @param $request
     * @return mixed
     */
    public function expense_chart_common_query($request){
        $from = $request['start_date'];
        $to = $request['end_date'];
        $date_type = $request['date_type'] ?? 'this_year';

        $order_query = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query){
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            });

        return self::date_wise_common_filter($order_query, $date_type, $from, $to);
    }

    /**
     * @param $query
     * @param $date_type
     * @param $from
     * @param $to
     * @return mixed
     */
    public function date_wise_common_filter($query, $date_type, $from, $to)
    {
        return $query->when(($date_type == 'this_year'), function ($query) {
            return $query->whereYear('created_at', date('Y'));
        })
            ->when(($date_type == 'this_month'), function ($query) {
                return $query->whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'));
            })
            ->when(($date_type == 'this_week'), function ($query) {
                return $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            })
            ->when(($date_type == 'custom_date' && !is_null($from) && !is_null($to)), function ($query) use ($from, $to) {
                return $query->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
            });
    }

    /**
     * @param Request $request
     * @return void
     */
    public function expense_summary_pdf(Request $request): void
    {
        $company_phone = $this->business_setting->where('key', 'phone')->first()->value ?? '';
        $company_email = $this->business_setting->where('key', 'email_address')->first()->value ?? '';
        $company_name = $this->business_setting->where('key', 'restaurant_name')->first()->value ?? '';
        $company_logo = $this->business_setting->where('key', 'logo')->first()->value ?? '';

        $search = $request['search'];
        $from = $request['start_date'];
        $to = $request['end_date'];
        $date_type = $request['date_type'] ?? 'this_year';

        $duration = str_replace('_', ' ', $date_type);
        if ($date_type == 'custom_date') {
            $duration = 'From ' . $from . ' To ' . $to;
        }

        $expense_report = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query){
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            });

        $expense_calculate = self::date_wise_common_filter($expense_report, $date_type, $from, $to)->get();

        $total_expense = 0;
        $extra_discount = 0;
        $free_delivery = 0;
        $free_delivery_over_amount = 0;
        $coupon_discount = 0;
        if($expense_calculate){
            foreach ($expense_calculate as $calculate){
                $extra_discount += $calculate->extra_discount;
                $free_delivery_over_amount += $calculate->free_delivery_amount;
                if(isset($calculate->coupon->coupon_type) && $calculate->coupon->coupon_type == 'free_delivery'){
                    $free_delivery += $calculate->coupon_discount_amount;
                }else{
                    $coupon_discount += $calculate->coupon_discount_amount;
                }
            }
        }
        $free_delivery += $free_delivery_over_amount;
        $total_expense = $extra_discount + $free_delivery + $coupon_discount;

        $data = [
            'total_expense' => $total_expense,
            'free_delivery' => $free_delivery,
            'coupon_discount' => $coupon_discount,
            'extra_discount' => $extra_discount,
            'company_phone' => $company_phone,
            'company_name' => $company_name,
            'company_email' => $company_email,
            'company_logo' => $company_logo,
            'duration' => $duration,
        ];

        //return $data;

        $mpdf_view = View::make('admin-views.report.expense-summary-pdf', compact('data'));
        Helpers::gen_mpdf($mpdf_view, 'expense-summary-report-', $date_type);

    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function expense_export_excel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $search = $request['search'];
        $from = $request['start_date'];
        $to = $request['end_date'];
        $date_type = $request['date_type'] ?? 'this_year';

        $expense_report = $this->order->with('coupon')
            ->where('order_status', 'delivered')
            ->where(function ($query){
                $query->whereNotIn('coupon_code', ['0', 'NULL'])
                    ->orWhere('free_delivery_amount', '>', 0)
                    ->orWhere('extra_discount', '>', 0);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('coupon_code', 'like', "%{$search}%");
            });

        $expense_list = self::date_wise_common_filter($expense_report, $date_type, $from, $to)->latest()->get();

        $data = [];

        foreach ($expense_list as $transaction) {
            $expense_amount = 0;
            if ($transaction->coupon_discount_amount > 0){
                $expense_amount = $transaction->coupon_discount_amount;
            }elseif ($transaction->extra_discount > 0){
                $expense_amount = $transaction->extra_discount;
            }elseif ($transaction->free_delivery_amount > 0){
                $expense_amount = $transaction->free_delivery_amount;
            }

            if (isset($transaction->coupon->coupon_type)){
                $type = $transaction->coupon->coupon_type;
            }elseif ($transaction->free_delivery_amount > 0){
                $type = 'Free Delivery';
            }elseif ($transaction->extra_discount > 0){
                $type = 'Extra Discount';
            }else{
                $type = 'Coupon Deleted';
            }

            $data[] = [
                'Order Date' => date_format($transaction->created_at, 'd F Y'),
                'Order ID' => $transaction->id,
                'Expense Amount' => Helpers::set_symbol($expense_amount),
                'Expense Type' => ucwords(str_replace('_', ' ', $type)),
            ];
        }
        return (new FastExcel($data))->download('expense_report.xlsx');

    }
}

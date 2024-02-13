<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Branch;
use App\Model\Category;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\User;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
       private Admin $admin,
       private Branch $branch,
       private Category $category,
       private Order $order,
       private OrderDetail $order_detail,
       private Product $product,
       private Review $review,
       private User $user
    ){}

    /**
     * @param $id
     * @return string
     */
    public function fcm($id): string
    {
        $fcm_token = $this->admin->find(auth('admin')->id())->fcm_token;
        $data = [
            'title' => 'New auto generate message arrived from admin dashboard',
            'description' => $id,
            'order_id' => '',
            'image' => '',
            'type' => 'order'
        ];

        try {
            Helpers::send_push_notif_to_device($fcm_token, $data);
            return "Notification sent to admin";
        } catch (\Exception $exception) {
            return "Notification send failed";
        }
    }

    /**
     * @return Factory|View|Application
     */
    public function dashboard(): View|Factory|Application
    {
        $top_sell = $this->order_detail->with(['product'])
            ->whereHas('order', function ($query){
                $query->where('order_status', 'delivered');
            })
            ->select('product_id', DB::raw('SUM(quantity) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();

        $most_rated_products = $this->review->with(['product'])
            ->select(['product_id',
                DB::raw('AVG(rating) as ratings_average'),
                DB::raw('COUNT(rating) as total'),
            ])
            ->groupBy('product_id')
            ->orderBy("total", 'desc')
            ->orderBy("ratings_average", 'desc')
            ->take(6)
            ->get();

        $top_customer = $this->order->with(['customer'])
            ->select('user_id', DB::raw('COUNT(user_id) as count'))
            ->groupBy('user_id')
            ->orderBy("count", 'desc')
            ->take(6)
            ->get();

        $data = self::order_stats_data();

        $data['customer'] = $this->user->count();
        $data['product'] = $this->product->count();
        $data['order'] = $this->order->count();
        $data['category'] = $this->category->where('parent_id', 0)->count();
        $data['branch'] = $this->branch->count();

        $data['pending_count'] = $this->order->where(['order_status' => 'pending'])->count();
        $data['ongoing_count'] = $this->order->whereIn('order_status', ['confirmed', 'processing', 'out_for_delivery'])->count();
        $data['delivered_count'] = $this->order->where(['order_status' => 'delivered'])->count();
        $data['canceled_count'] = $this->order->where(['order_status' => 'canceled'])->count();
        $data['returned_count'] = $this->order->where(['order_status' => 'returned'])->count();
        $data['failed_count'] = $this->order->where(['order_status' => 'failed'])->count();

        $data['recent_orders'] = $this->order->notPos()->latest()->take(5)->get(['id', 'created_at', 'order_status']);


        $data['top_sell'] = $top_sell;
        $data['most_rated_products'] = $most_rated_products;
        $data['top_customer'] = $top_customer;

        $from = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        /*earning statistics chart*/

        $earning = [];
        $earning_data = $this->order->where([
            'order_status' => 'delivered'
        ])->select(
            DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();
        for ($inc = 1; $inc <= 12; $inc++) {
            $earning[$inc] = 0;
            foreach ($earning_data as $match) {
                if ($match['month'] == $inc) {
                    $earning[$inc] = $match['sums'];
                }
            }
        }

        /*order statistics chart*/

        $order_statistics_chart = [];
        $order_statistics_chart_data = $this->order->where(['order_status' => 'delivered'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();


        for ($inc = 1; $inc <= 12; $inc++) {
            $order_statistics_chart[$inc] = 0;
            foreach ($order_statistics_chart_data as $match) {
                if ($match['month'] == $inc) {
                    $order_statistics_chart[$inc] = $match['total'];
                }
            }
        }

        //dd($order_statistics_chart_data);
        return view('admin-views.dashboard', compact('data', 'earning', 'order_statistics_chart'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function order_stats(Request $request): \Illuminate\Http\JsonResponse
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::order_stats_data();

        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    /**
     * @return array
     */
    public function order_stats_data(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $pending = $this->order->where(['order_status' => 'pending'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', \Carbon\Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $confirmed = $this->order->where(['order_status' => 'confirmed'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $processing = $this->order->where(['order_status' => 'processing'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $out_for_delivery = $this->order->where(['order_status' => 'out_for_delivery'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $delivered = $this->order->where(['order_status' => 'delivered'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $all = $this->order->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $returned = $this->order->where(['order_status' => 'returned'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();
        $failed = $this->order->where(['order_status' => 'failed'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        $canceled = $this->order->where(['order_status' => 'canceled'])
            ->when($today, function ($query) {
                return $query->whereDate('created_at', Carbon::today());
            })
            ->when($this_month, function ($query) {
                return $query->whereMonth('created_at', Carbon::now());
            })
            ->count();

        return $data = [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $out_for_delivery,
            'delivered' => $delivered,
            'all' => $all,
            'returned' => $returned,
            'failed' => $failed,
            'canceled' => $canceled
        ];

    }

    /**
     * filter order statistics in week, month, year by ajax
     */
    public function get_order_statitics(Request $request): \Illuminate\Http\JsonResponse
    {
        $dateType = $request->type;

        $order_data = array();
        if($dateType == 'yearOrder') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $orders = $this->order->where(['order_status' => 'delivered'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $order_data[$inc] = 0;
                foreach ($orders as $match) {
                    if ($match['month'] == $inc) {
                        $order_data[$inc] = $match['total'];
                    }
                }
            }
            $key_range = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        }elseif($dateType == 'MonthOrder') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d',strtotime($to));
            $key_range = range(1, $number);

            $orders = $this->order->where(['order_status' => 'delivered'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $order_data[$inc] = 0;
                foreach ($orders as $match) {
                    if ($match['day'] == $inc) {
                        $order_data[$inc] = $match['total'];
                    }
                }
            }

        }elseif($dateType == 'WeekOrder') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek()->format('Y-m-d 00:00:00');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d 23:59:59');
            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $day_range = array();
            foreach($date_range as $date){
                $day_range[] =$date->format('d');
            }
            $day_range = array_flip($day_range);
            $day_range_keys = array_keys($day_range);
            $day_range_values = array_values($day_range);
            $day_range_intKeys = array_map('intval', $day_range_keys);
            $day_range = array_combine($day_range_intKeys, $day_range_values);

            $orders = $this->order->where(['order_status' => 'delivered'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->orderBy('created_at', 'ASC')->pluck('total', 'day')->toArray();

            $order_data = array();
            foreach($day_range as $day=>$value){
                $day_value = 0;
                $order_data[$day] = $day_value;
            }

            foreach($orders as $order_day => $order_value){
                if(array_key_exists($order_day, $order_data)){
                    $order_data[$order_day] = $order_value;
                }
            }
            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        }

        $label = $key_range;
        $order_data_final = $order_data;

        $data = array(
            'orders_label' => $label,
            'orders' => array_values($order_data_final),
        );
        return response()->json($data);
    }

    /**
     * filter earning statistics in week, month, year by ajax
     */
    public function get_earning_statitics(Request $request): \Illuminate\Http\JsonResponse
    {
        $dateType = $request->type;

        $earning_data = array();
        if($dateType == 'yearEarn') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $earning = $this->order->where([
                'order_status' => 'delivered'
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $earning_data[$inc] = 0;
                foreach ($earning as $match) {
                    if ($match['month'] == $inc) {
                        $earning_data[$inc] = $match['sums'];
                    }
                }
            }
            $key_range = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");


        }elseif($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d',strtotime($to));
            $key_range = range(1, $number);

            $earning = $this->order->where([
                'order_status' => 'delivered'
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $earning_data[$inc] = 0;
                foreach ($earning as $match) {
                    if ($match['day'] == $inc) {
                        $earning_data[$inc] = $match['sums'];
                    }
                }
            }

        }elseif($dateType == 'WeekEarn') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek()->format('Y-m-d 00:00:00');
            $to = Carbon::now()->endOfWeek()->format('Y-m-d 23:59:59');
            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $day_range = array();
            foreach($date_range as $date){
                $day_range[] =$date->format('d');
            }
            $day_range = array_flip($day_range);
            $day_range_keys = array_keys($day_range);
            $day_range_values = array_values($day_range);
            $day_range_intKeys = array_map('intval', $day_range_keys);
            $day_range = array_combine($day_range_intKeys, $day_range_values);

            $earning = $this->order->where([
                'order_status' => 'delivered'
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('day')->orderBy('created_at', 'ASC')->pluck('sums', 'day')->toArray();

            $earning_data = array();
            foreach($day_range as $day=>$value){
                $day_value = 0;
                $earning_data[$day] = $day_value;
            }

            foreach($earning as $order_day => $order_value){
                if(array_key_exists($order_day, $earning_data)){
                    $earning_data[$order_day] = $order_value;
                }
            }

            $key_range = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        }

        $label = $key_range;
        $earning_data_final = $earning_data;

        $data = array(
            'earning_label' => $label,
            'earning' => array_values($earning_data_final),
        );
        return response()->json($data);
    }

}

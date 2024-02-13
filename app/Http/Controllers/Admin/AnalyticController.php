<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\RecentSearch;
use App\Model\SearchedCategory;
use App\Model\SearchedData;
use App\Model\SearchedKeywordCount;
use App\Model\SearchedKeywordUser;
use App\Model\SearchedProduct;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Rap2hpoutre\FastExcel\FastExcel;

class AnalyticController extends Controller
{
    public function __construct(
        private RecentSearch $recent_search,
        private SearchedData $searched_data,
        private User $customer,
        private SearchedKeywordCount $searched_keyword_count,
        private SearchedCategory $searched_category,
        private SearchedKeywordUser $searched_keyword_user,
        private SearchedProduct $searched_product
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function get_keyword_search(Request $request): View|Factory|Application
    {
        Validator::make($request->all(), [
            'date_range' => 'in:today,all_time,this_week,last_week,this_month,last_month,last_15_days,this_year,last_year,last_6_month,this_year_1st_quarter,this_year_2nd_quarter,this_year_3rd_quarter,this_year_4th_quarter',
            'date_range_2' => 'in:today,all_time,this_week,last_week,this_month,last_month,last_15_days,this_year,last_year,last_6_month,this_year_1st_quarter,this_year_2nd_quarter,this_year_3rd_quarter,this_year_4th_quarter',
        ]);

        //params
        $search = $request['search'];
        $query_params = ['search' => $search];
        if ($request->has('date_range')) {
            $query_params['date_range'] = $request['date_range'];
        }
        else{
            $query_params['date_range'] = 'today';
        }

        if ($request->has('date_range_2')) {
            $query_params['date_range_2'] = $request['date_range_2'];
        }
        else{
            $query_params['date_range_2'] = 'today';
        }

        //*** graph data ***

        //Trending Keywords
        $recent_search_count = $this->recent_search
            ->with(['searched_user'])
            ->when($request->has('date_range'), function ($query) use ($request) {
                //DATE RANGE
                if ($request['date_range'] == 'today') {
                    // today's data
                    $query->whereDate('created_at', Carbon::now()->toDateString());
                }
                elseif ($request['date_range'] == 'this_week') {
                    //this week
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                } elseif ($request['date_range'] == 'last_week') {
                    //last week
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);

                } elseif ($request['date_range'] == 'this_month') {
                    //this month
                    $query->whereMonth('created_at', Carbon::now()->month);

                } elseif ($request['date_range'] == 'last_month') {
                    //last month
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);

                } elseif ($request['date_range'] == 'last_15_days') {
                    //last 15 days
                    $query->whereBetween('created_at', [Carbon::now()->subDay(15), Carbon::now()]);

                } elseif ($request['date_range'] == 'this_year') {
                    //this year
                    $query->whereYear('created_at', Carbon::now()->year);

                } elseif ($request['date_range'] == 'last_year') {
                    //last year
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);

                } elseif ($request['date_range'] == 'last_6_month') {
                    //last 6month
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()]);

                } elseif ($request['date_range'] == 'this_year_1st_quarter') {
                    //this year 1st quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(1)->startOfQuarter(), Carbon::now()->month(1)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_2nd_quarter') {
                    //this year 2nd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(4)->startOfQuarter(), Carbon::now()->month(4)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_3rd_quarter') {
                    //this year 3rd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(7)->startOfQuarter(), Carbon::now()->month(7)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_4th_quarter') {
                    //this year 4th quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(10)->startOfQuarter(), Carbon::now()->month(10)->endOfQuarter()]);
                }
            })
            ->select('keyword', DB::raw('count(*) as count'))
            ->groupBy('keyword')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $graph_total = 0;
        foreach ($recent_search_count as $item) {
            $graph_total += $item['count'];
        }

        $graph_data = ['keyword' => [], 'count' => [], 'avg' => []];
        foreach ($recent_search_count as $item) {
            $graph_data['keyword'][] = Str::limit($item['keyword'], 13);
            $graph_data['count'][] = $item['count'];
            $graph_data['avg'][] = number_format($item['count']*100/$graph_total ?? 0.0, 2);
        }

        $searched_keyword_count = $this->searched_keyword_count
            ->when($request->has('date_range_2'), function ($query) use ($request) {
                //DATE RANGE
                if ($request['date_range_2'] == 'today') {
                    // today's data
                    $query->whereDate('created_at', Carbon::now()->toDateString());
                }
                elseif ($request['date_range_2'] == 'this_week') {
                    //this week
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'last_week') {
                    //last week
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'this_month') {
                    //this month
                    $query->whereMonth('created_at', Carbon::now()->month);

                } elseif ($request['date_range_2'] == 'last_month') {
                    //last month
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);

                } elseif ($request['date_range_2'] == 'last_15_days') {
                    //last 15 days
                    $query->whereBetween('created_at', [Carbon::now()->subDay(15), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year') {
                    //this year
                    $query->whereYear('created_at', Carbon::now()->year);

                } elseif ($request['date_range_2'] == 'last_year') {
                    //last year
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);

                } elseif ($request['date_range_2'] == 'last_6_month') {
                    //last 6month
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year_1st_quarter') {
                    //this year 1st quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(1)->startOfQuarter(), Carbon::now()->month(1)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_2nd_quarter') {
                    //this year 2nd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(4)->startOfQuarter(), Carbon::now()->month(4)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_3rd_quarter') {
                    //this year 3rd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(7)->startOfQuarter(), Carbon::now()->month(7)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_4th_quarter') {
                    //this year 4th quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(10)->startOfQuarter(), Carbon::now()->month(10)->endOfQuarter()]);
                }
            })
            ->sum('keyword_count');


        //Category Wise Search Volume
        $category_wise_volumes = $this->searched_category
            ->when($request->has('date_range_2'), function ($query) use ($request) {
                //DATE RANGE
                if ($request['date_range_2'] == 'today') {
                    // today's data
                    $query->whereDate('created_at', Carbon::now()->toDateString());
                }
                elseif ($request['date_range_2'] == 'this_week') {
                    //this week
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'last_week') {
                    //last week
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'this_month') {
                    //this month
                    $query->whereMonth('created_at', Carbon::now()->month);

                } elseif ($request['date_range_2'] == 'last_month') {
                    //last month
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);

                } elseif ($request['date_range_2'] == 'last_15_days') {
                    //last 15 days
                    $query->whereBetween('created_at', [Carbon::now()->subDay(15), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year') {
                    //this year
                    $query->whereYear('created_at', Carbon::now()->year);

                } elseif ($request['date_range_2'] == 'last_year') {
                    //last year
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);

                } elseif ($request['date_range_2'] == 'last_6_month') {
                    //last 6month
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year_1st_quarter') {
                    //this year 1st quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(1)->startOfQuarter(), Carbon::now()->month(1)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_2nd_quarter') {
                    //this year 2nd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(4)->startOfQuarter(), Carbon::now()->month(4)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_3rd_quarter') {
                    //this year 3rd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(7)->startOfQuarter(), Carbon::now()->month(7)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_4th_quarter') {
                    //this year 4th quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(10)->startOfQuarter(), Carbon::now()->month(10)->endOfQuarter()]);
                }
            })
            ->whereNotNull('category_id')
            ->with('category')
            ->groupBy('category_id')
            ->select('category_id', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $total = 0;
        foreach ($category_wise_volumes as $item) {
            $total += $item['count'];
        }

        //*** table data ***

        $searched_table_data = $this->recent_search
            ->with(['volume', 'searched_category', 'searched_category.category', 'searched_product' ])
            ->withCount('volume','searched_category', 'searched_product')
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    return $query->where('keyword', 'like', '%' . $key . '%');
                }
            })
            ->orderBy('volume_count', 'desc')
            ->paginate(Helpers::getPagination())
            ->appends($query_params);

        //return $searched_table_data;
        return view('admin-views.analytics.keyword-search', compact('query_params', 'graph_data', 'search', 'category_wise_volumes', 'total', 'searched_keyword_count', 'searched_table_data'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     * @throws ValidationException
     */
    public function get_customer_search(Request $request): View|Factory|Application
    {
        Validator::make($request->all(), [
            'date_range' => 'in:today,all_time,this_week,last_week,this_month,last_month,last_15_days,this_year,last_year,last_6_month,this_year_1st_quarter,this_year_2nd_quarter,this_year_3rd_quarter,this_year_4th_quarter',
            'date_range_2' => 'in:today,all_time,this_week,last_week,this_month,last_month,last_15_days,this_year,last_year,last_6_month,this_year_1st_quarter,this_year_2nd_quarter,this_year_3rd_quarter,this_year_4th_quarter',
        ])->validate();

        //params
        $search = $request['search'];
        $query_params = ['search' => $search];
        if ($request->has('date_range')) {
            $query_params['date_range'] = $request['date_range'];
        }
        else{
            $query_params['date_range'] = 'today';
        }

        if ($request->has('date_range_2')) {
            $query_params['date_range_2'] = $request['date_range_2'];
        }
        else{
            $query_params['date_range_2'] = 'today';
        }

        //*** Graph Data **

        //Top customers
        $top_customer = $this->searched_keyword_user->with(['customer'])
            ->when($request->has('date_range'), function ($query) use ($request) {
                //DATE RANGE
                if ($request['date_range'] == 'today') {
                    // today's data
                    $query->whereDate('created_at', Carbon::now()->toDateString());
                }
                elseif ($request['date_range'] == 'this_week') {
                    //this week
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                } elseif ($request['date_range'] == 'last_week') {
                    //last week
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);

                } elseif ($request['date_range'] == 'this_month') {
                    //this month
                    $query->whereMonth('created_at', Carbon::now()->month);

                } elseif ($request['date_range'] == 'last_month') {
                    //last month
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);

                } elseif ($request['date_range'] == 'last_15_days') {
                    //last 15 days
                    $query->whereBetween('created_at', [Carbon::now()->subDay(15), Carbon::now()]);

                } elseif ($request['date_range'] == 'this_year') {
                    //this year
                    $query->whereYear('created_at', Carbon::now()->year);

                } elseif ($request['date_range'] == 'last_year') {
                    //last year
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);

                } elseif ($request['date_range'] == 'last_6_month') {
                    //last 6month
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()]);

                } elseif ($request['date_range'] == 'this_year_1st_quarter') {
                    //this year 1st quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(1)->startOfQuarter(), Carbon::now()->month(1)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_2nd_quarter') {
                    //this year 2nd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(4)->startOfQuarter(), Carbon::now()->month(4)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_3rd_quarter') {
                    //this year 3rd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(7)->startOfQuarter(), Carbon::now()->month(7)->endOfQuarter()]);

                } elseif ($request['date_range'] == 'this_year_4th_quarter') {
                    //this year 4th quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(10)->startOfQuarter(), Carbon::now()->month(10)->endOfQuarter()]);
                }
            })
            ->select(
                DB::raw('count(recent_search_id) as count'),
                DB::raw('user_id')
            )
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->take(5)
            ->get();

        //return $top_customer;

        $graph_data = ['top_customers' => [], 'search_volume' => []];
        foreach ($top_customer as $item) {
            $graph_data['top_customers'][] = $item->customer ? $item->customer->f_name . ' ' . $item->customer->l_name :  '';
            $graph_data['search_volume'][] = $item->count;
        }

        //Top Products
        $top_products = $this->searched_product->
            when($request->has('date_range_2'), function ($query) use ($request) {
                //DATE RANGE
                if ($request['date_range_2'] == 'today') {
                    // today's data
                    $query->whereDate('created_at', Carbon::now()->toDateString());
                }
                elseif ($request['date_range_2'] == 'this_week') {
                    //this week
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'last_week') {
                    //last week
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);

                } elseif ($request['date_range_2'] == 'this_month') {
                    //this month
                    $query->whereMonth('created_at', Carbon::now()->month);

                } elseif ($request['date_range_2'] == 'last_month') {
                    //last month
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);

                } elseif ($request['date_range_2'] == 'last_15_days') {
                    //last 15 days
                    $query->whereBetween('created_at', [Carbon::now()->subDay(15), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year') {
                    //this year
                    $query->whereYear('created_at', Carbon::now()->year);

                } elseif ($request['date_range_2'] == 'last_year') {
                    //last year
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);

                } elseif ($request['date_range_2'] == 'last_6_month') {
                    //last 6month
                    $query->whereBetween('created_at', [Carbon::now()->subMonth(6), Carbon::now()]);

                } elseif ($request['date_range_2'] == 'this_year_1st_quarter') {
                    //this year 1st quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(1)->startOfQuarter(), Carbon::now()->month(1)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_2nd_quarter') {
                    //this year 2nd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(4)->startOfQuarter(), Carbon::now()->month(4)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_3rd_quarter') {
                    //this year 3rd quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(7)->startOfQuarter(), Carbon::now()->month(7)->endOfQuarter()]);

                } elseif ($request['date_range_2'] == 'this_year_4th_quarter') {
                    //this year 4th quarter
                    $query->whereBetween('created_at', [Carbon::now()->month(10)->startOfQuarter(), Carbon::now()->month(10)->endOfQuarter()]);
                }
            })
            ->whereNotNull('product_id')
            ->with('product')
            ->groupBy('product_id')
            ->select('product_id', DB::raw('count(*) as count'))
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();
        //return $top_products;
        $total = 0;
        foreach ($top_products as $top_product) {
            $total += $top_product['count'];
        }

        //*** Table Data **

        $customers_data = $this->searched_keyword_user->with(['customer', 'related_category', 'related_product', 'related_category.category'])
            ->when($request->has('search'), function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                $query->whereHas('customer', function ($query) use ($keys) {
                    $query->where(function ($query) use ($keys) {
                        foreach ($keys as $key) {
                            $query->orWhere('f_name', 'like', '%'.$key.'%')
                                ->orWhere('l_name', 'like', '%'.$key.'%');
                        }
                    });
                });
            })
            ->select('user_id', DB::raw('count(*) as search_volume'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->withCount('visited_products', 'orders', 'related_category', 'related_product')
            ->orderBy('search_volume', 'desc')
            ->paginate(Helpers::getPagination())->appends($query_params);

       // return $customers_data;

        return view('admin-views.analytics.customer-search', compact('customers_data','query_params', 'top_customer', 'top_products', 'graph_data', 'search', 'total'));
    }

    public function export_keyword_search(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $searched_list = $this->recent_search
            ->with(['volume', 'searched_category', 'searched_category.category', 'searched_product' ])
            ->withCount('volume','searched_category', 'searched_product')
            ->latest()
            ->get();

        $data = [];
        foreach ($searched_list as $list) {
            $data[] = [
                'Keyword' => $list->keyword,
                'Search Volume' => $list->volume_count,
                'Related Categories' =>  $list->searched_category_count,
                'Related Products' =>  $list->searched_product_count,
            ];
        }
        return (new FastExcel($data))->download('keyword-search.xlsx');
    }

    public function export_customer_search(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $customers_list = $this->searched_keyword_user->with(['customer', 'related_category', 'related_product', 'related_category.category'])
            ->select('user_id', DB::raw('count(*) as search_volume'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->withCount('visited_products', 'orders', 'related_category', 'related_product')
            ->latest()
            ->get();

        $data = [];
        foreach ($customers_list as $list) {
            $data[] = [
                'Customer' => $list->customer['f_name']." ".$list->customer['l_name'],
                'Search Volume' => $list->search_volume,
                'Related Categories' =>  $list->related_category_count,
                'Related Products' =>  $list->related_product_count,
                'Times Products Visited' =>  $list->visited_products_count,
                'Total Orders' =>  $list->orders_count,
            ];
        }
        return (new FastExcel($data))->download('customer-search.xlsx');
    }
}

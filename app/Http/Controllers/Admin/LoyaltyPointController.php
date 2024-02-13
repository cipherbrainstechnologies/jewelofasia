<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\LoyaltyTransaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LoyaltyPointController extends Controller
{
    public function __construct(
        private LoyaltyTransaction $loyalty_transaction
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function report(Request $request): View|Factory|Application
    {
        $data = $this->loyalty_transaction->selectRaw('sum(credit) as total_credit, sum(debit) as total_debit')
            ->when(($request->from && $request->to),function($query)use($request){
                $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
            })
            ->when($request->transaction_type, function($query)use($request){
                $query->where('transaction_type',$request->transaction_type);
            })
            ->when($request->customer_id, function($query)use($request){
                $query->where('user_id',$request->customer_id);
            })
            ->get();

        $transactions = $this->loyalty_transaction->with(['customer'])
            ->when(($request->from && $request->to),function($query)use($request){
                $query->whereBetween('created_at', [$request->from.' 00:00:00', $request->to.' 23:59:59']);
            })
            ->when($request->transaction_type, function($query)use($request){
                $query->where('transaction_type',$request->transaction_type);
            })
            ->when($request->customer_id, function($query)use($request){
                $query->where('user_id',$request->customer_id);
            })
            ->latest()
            ->paginate(Helpers::getPagination());

        return view('admin-views.customer.loyalty-point.report', compact('data','transactions'));
    }
}

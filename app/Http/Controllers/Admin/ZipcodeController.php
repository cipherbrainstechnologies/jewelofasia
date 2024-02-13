<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zipcodes;
use App\Models\City;
use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;


class ZipcodeController extends Controller
{

    public function __construct(
        private Zipcodes $zipcodes
    ){}

    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $zipcodes = $this->zipcodes->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('zipcode', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $zipcodes = $this->zipcodes;
        }
        $city = City::where('status','active')->get();
        $days = Helpers::days();//get days from helpers
        $zipcodes = $zipcodes->with('city')->orderBY('id', 'ASC')->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.zipcodes.index', compact('zipcodes', 'search','city','days'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {   

        // Put server side Validator
        $request->validate([
            'zipcode.*' => 'required',
            'order_before_day' => 'required',
            'delivery_order_day' => 'required'

        ],[            
            'zipcode.*.required' => 'Zipcode is required',
            'order_before_day.required' => 'Order before day is required',
            'delivery_order_day.required' => 'Delivery order day is required' 
        ]);

        $zipcode = $this->zipcodes;
        $zipcode->city_id = $request->city;
        $zipcode->zipcode = $request->zipcode[0];
        $zipcode->order_before_day = $request->order_before_day;
        $zipcode->delivery_order_day = implode(',',$request->delivery_order_day);
        $zipcode->save();

        Toastr::success(translate('Zipcode created successfully'));
        return back();
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {   
        $zipcodes = $this->zipcodes->with('translations')->find($id);
        $city = City::where('status','active')->get();
        $days = Helpers::days();
        return view('admin-views.zipcodes.edit', compact('zipcodes','city','days'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_before_day' => 'required',
            'delivery_order_day' => 'required'

        ],[            
            'order_before_day.required' => 'Order before day is required',
            'delivery_order_day.required' => 'Delivery order day is required' 
        ]);

        //    Validator of zipcodes
        $zipcode = $this->zipcodes->find($id);
        $zipcode->city_id = $request->city;
        $zipcode->zipcode = $request->zipcode[0];
        $zipcode->order_before_day = $request->order_before_day;
        $zipcode->delivery_order_day = implode(',',$request->delivery_order_day);
        $zipcode->save();

        Toastr::success(translate('Zipcode updated successfully'));
        return back();

    }

    public function delete($id)
    {
        $zipcode = $this->zipcodes->find($id);
        if(!empty($zipcode)){
            $zipcode->delete();
            Toastr::success(translate('Zipcode removed!'));
        }
        return back();

    }
}

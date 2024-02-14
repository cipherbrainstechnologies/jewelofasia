<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;

class CityController extends Controller
{

    public function __construct(
        private City $city
    ){}
    
    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $city = $this->city->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $city = $this->city;
        }
        $city = $city->orderBY('id', 'ASC')->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.cities.index', compact('city', 'search'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:cities',
        ],[
            'name.unique' => 'The city name has already been taken.',
        ]);

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                toastr::error(translate('Name is too long!'));
                return back();
            }
        }
        $ct = $this->city;
        $ct->name = $request->name[array_search('en', $request->lang)];
        $ct->status = $request->status;
        $ct->save();
        
        Toastr::success(translate('City created successfully'));
        return back();
    }

    
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $city = $this->city->with('translations')->find($id);
        return view('admin-views.cities.edit', compact('city'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:cities',
        ],[
            'name.unique' => 'The city name has already been taken.',
        ]);
        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                toastr::error(translate('Name is too long!'));
                return back();
            }
        }
        $ct = $this->city->find($id);
        $ct->name = $request->name[array_search('en', $request->lang)];
        $ct->status = $request->status;
        $ct->save();
        // foreach ($request->lang as $index => $key) {
        //     if ($key != 'en') {
        //         Translation::updateOrInsert(
        //             ['translationable_type' => 'App\Model\City',
        //                 'translationable_id' => $ct->id,
        //                 'locale' => $key,
        //                 'key' => 'name'],
        //             ['value' => $request->name[$index]]
        //         );
        //     }
        // }
        Toastr::success(translate('City updated successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $city = $this->city->find($id);
        if(!empty($city)){
            $city->delete();
            Toastr::success(translate('City removed!'));
        }
        return back();
    }
}

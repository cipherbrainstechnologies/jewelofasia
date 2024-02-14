<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Zipcodes;

class CityController extends Controller
{

    public function __construct(
        private City $city,
        private Zipcodes $zipcode
    ){}

    public function getAllCities(Request $request){
        try{
            $cities = $this->city->active()->get();
            return response()->json($cities,200);
        }catch(\Exception $e){
            return response()->json([], 200);
        }
    }

    public function getCity($cityId){
        try{
            $cityDetail = $this->city->find($cityId);
            return response()->json($cityDetail,200);
        }catch(\Exception $e){
            return response()->json([], 200);
        }
    }

    public function getZipcodes($cityId){
        try{
            $zipcodesList = $this->zipcode->where('city_id',$cityId)->active()->get();
            return response()->json($zipcodesList,200);
        }catch(\Exception $e){
            return response()->json([], 200);
        }
    }
}

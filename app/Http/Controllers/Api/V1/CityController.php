<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\ZipCodes;
use Carbon\Carbon;

class CityController extends Controller
{

    public function __construct(
        private City $city,
        private ZipCodes $zipcode
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

    // public function DeliveryDetail($cityId,$zipcode){
    //     $today = now();
    //     $cityDetail = $this->city->with('zipcodes')->find($cityId);
    //     $expectedDates = [];
    //     if(!empty($cityDetail)){
    //        $deliveryDays = explode(',',$cityDetail->zipcodes[0]['delivery_order_day']);
    //        echo "<pre>";print_r($deliveryDays);
    //        $orderBeforeDay = strtolower($cityDetail->zipcodes[0]['order_before_day']);

    //         $dayMappings = [
    //             'sunday' => Carbon::SUNDAY,
    //             'monday' => Carbon::MONDAY,
    //             'tuesday' => Carbon::TUESDAY,
    //             'wednesday' => Carbon::WEDNESDAY,
    //             'thursday' => Carbon::THURSDAY,
    //             'friday' => Carbon::FRIDAY,
    //             'saturday' => Carbon::SATURDAY,
    //         ];

    //         $carbonDay = $dayMappings[$orderBeforeDay] ?? null;

    //         // $inputCarbon = Carbon::parse($inputDate);
    //         if ($today->dayOfWeek < $dayMappings[$orderBeforeDay]) {
    //             foreach ($deliveryDays as $key => $day) {
    //                 $expectedDates[$key] = $today->next(ucfirst($day))->format('Y-m-d');
    //             }
                
    //         } else {
    //             foreach ($deliveryDays as $key => $day) {
    //                 $expectedDates[$key] = $today->next(ucfirst($day))->addWeek()->format('Y-m-d');
    //             }
    //         }
    //         echo "<pre>";print_r($expectedDates);die;

    //     }
    // }
    public function DeliveryDetail($cityId, $zipcode)
    {
        $today = now();
        $cityDetail = $this->city->with('zipcodes')->find($cityId);
        $expectedDates = [];

        if (!empty($cityDetail)) {
            $deliveryDays = explode(',', $cityDetail->zipcodes[0]['delivery_order_day']);
            $orderBeforeDay = strtolower($cityDetail->zipcodes[0]['order_before_day']);

            $dayMappings = [
                'sunday' => Carbon::SUNDAY,
                'monday' => Carbon::MONDAY,
                'tuesday' => Carbon::TUESDAY,
                'wednesday' => Carbon::WEDNESDAY,
                'thursday' => Carbon::THURSDAY,
                'friday' => Carbon::FRIDAY,
                'saturday' => Carbon::SATURDAY,
            ];

            $carbonDay = $dayMappings[$orderBeforeDay] ?? null;

            // Check if today is before the order before day
            if ($today->dayOfWeek < $dayMappings[$orderBeforeDay]) {
                foreach ($deliveryDays as $key => $day) {
                    $expectedDates[$key] = $today->next($dayMappings[$day])->format('Y-m-d') .' '.$today->next($dayMappings[$day])->format('l').')';
                }
            } else {
                $nextWeek = $today->next($orderBeforeDay);
                foreach ($deliveryDays as $key => $day) {
                    $nextDeliveryDate = $nextWeek->next($day);
                    $expectedDates[$key] =   $nextDeliveryDate->format('Y-m-d').' ('.$nextDeliveryDate->format('l').')';
                }
            }
            return response()->json(['expected_dates' => $expectedDates], 200);
        }
    }

}

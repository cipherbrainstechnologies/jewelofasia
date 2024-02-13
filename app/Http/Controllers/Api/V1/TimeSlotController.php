<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\TimeSlot;
use Illuminate\Http\JsonResponse;

class TimeSlotController extends Controller
{
    public function __construct(
        private TimeSlot $time_slot
    ){}

    /**
     * @return JsonResponse
     */
    public function getTime_slot(): JsonResponse
    {
        try {
            return response()->json($this->time_slot->active()->orderBy('start_time', 'asc')->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

}

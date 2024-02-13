<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Model\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private Notification $notification
    ){}

    public function get_notifications(): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json($this->notification->active()->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}

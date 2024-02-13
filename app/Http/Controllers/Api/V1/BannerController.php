<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(
        private Banner $banner
    ){}

    /**
     * @return JsonResponse
     */
    public function get_banners(): JsonResponse
    {
        try {
            return response()->json($this->banner->active()->get(), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
}

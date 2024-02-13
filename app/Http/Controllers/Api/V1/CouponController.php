<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function __construct(
        private Coupon $coupon,
        private Order $order
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        if (auth('api')->user()) {
            $coupon = $this->coupon->active()
                ->where(function($query) use ($request) {
                    $query->where('customer_id', auth('api')->user()->id)
                        ->orWhere('customer_id', null);
                })
                ->latest()
                ->get();
        } else {
            $coupon = $this->coupon->active()->default()->latest()->get();
        }

        return response()->json($coupon, 200);

    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->errors()->count()>0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $coupon = $this->coupon->active()->where(['code' => $request['code']])->first();
            if (isset($coupon)) {

                //default coupon type
                if ($coupon['coupon_type'] == 'default') {
                    $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
                    $user_type = (bool)auth('api')->user() ? 0 : 1;

                    $total = $this->order->where(['user_id' => $user_id, 'coupon_code' => $request['code'], 'is_guest' => $user_type])->count();
                    if ($total < $coupon['limit']) {
                        return response()->json($coupon, 200);
                    }else{
                        return response()->json([
                            'errors' => [['code' => 'coupon', 'message' => translate('coupon limit is over')]]], 403);
                    }
                }

                //first order coupon type
                if($coupon['coupon_type'] == 'first_order') {
                    $first_order = $this->order->where(['user_id' => auth('api')->user()->id, 'is_guest' => 0])->count();
                    $total = $this->order->where(['user_id' => auth('api')->user()->id, 'coupon_code' => $request['code'], 'is_guest' => 0 ])->count();
                    if ($total == 0 && $first_order == 0) {
                        return response()->json($coupon, 200);
                    }else{
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 403);
                    }
                }

                //free delivery
                if($coupon['coupon_type'] == 'free_delivery') {
                    $total = $this->order->where(['user_id' => auth('api')->user()->id, 'coupon_code' => $request['code'], 'is_guest' => 0 ])->count();
                    if ($total < $coupon['limit']) {
                        return response()->json($coupon, 200);
                    }else{
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 403);
                    }
                }

                //customer wise
                if($coupon['coupon_type'] == 'customer_wise') {

                    $total = $this->order->where(['user_id' => auth('api')->user()->id, 'coupon_code' => $request['code'], 'is_guest' => 0 ])->count();

                    if ($coupon['customer_id'] != auth('api')->user()->id){
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 403);
                    }

                    if ($total < $coupon['limit']) {
                        return response()->json($coupon, 200);
                    }else{
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 403);
                    }
                }

            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => 'not found!']
                    ]
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }
}

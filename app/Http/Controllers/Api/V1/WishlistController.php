<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{

    public function __construct(
        private Wishlist $wishlist
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_to_wishlist(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = $this->wishlist->where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (empty($wishlist)) {
            $wishlist = $this->wishlist;
            $wishlist->user_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;
            $wishlist->save();
            return response()->json(['message' => 'successfully added!'], 200);
        }

        return response()->json(['message' => 'Already in your wishlist'], 409);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_from_wishlist(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = $this->wishlist->where('user_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            $this->wishlist->where(['user_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);

        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    public function wish_list(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->wishlist->where('user_id', $request->user()->id)->get(), 200);
    }
}

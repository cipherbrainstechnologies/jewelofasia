<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GuestUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestUserController extends Controller
{
    public function __construct(
        private GuestUser $guest_user,
    ){}


    public function guest_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('guest_id') && $request->guest_id != null){
            $guest = GuestUser::find($request->guest_id);
            if (isset($guest)){
                $guest->fcm_token = null;
                $guest->save();
            }
            return response()->json(null, 200);
        }else{
            $guest = new GuestUser();
            $guest->ip_address = $request->ip();
            $guest->fcm_token = $request->fcm_token;
            $guest->language_code = $request->header('X-localization') ?? 'en';
            $guest->save();

            return response()->json(['guest' => $guest], 200);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Conversation;
use App\Model\CustomerAddress;
use App\Model\Newsletter;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Models\GuestUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Http\JsonResponse;


class CustomerController extends Controller
{
    public function __construct(
        private Conversation $conversation,
        private CustomerAddress $customer_address,
        private Newsletter $newsletter,
        private Order $order,
        private OrderDetail $order_detail,
        private User $user,
        private GuestUser $guest_user
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function address_list(Request $request): JsonResponse
    {
        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        return response()->json($this->customer_address->where(['user_id' => $user_id, 'is_guest' => $user_type])->latest()->get(), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add_new_address(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (auth('api')->user() || $request->header('guest-id')){
            $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
            $user_type = (bool)auth('api')->user() ? 0 : 1;

            $address = [
                'user_id' => $user_id,
                'is_guest' => $user_type,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_number' => $request->contact_person_number,
                'address_type' => $request->address_type,
                'address' => $request->address,
                'road' => $request->road,
                'house' => $request->house,
                'floor' => $request->floor,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'created_at' => now(),
                'updated_at' => now()
            ];
            DB::table('customer_addresses')->insert($address);
            return response()->json(['message' => 'successfully added!'], 200);
        }
        return response()->json(['message' => 'no user data found!'], 403);

    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update_address(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        $address = [
            'user_id' => $user_id,
            'is_guest' => $user_type,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('id',$id)->update($address);
        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_address(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = (bool)auth('api')->user() ? auth('api')->user()->id : $request->header('guest-id');
        $user_type = (bool)auth('api')->user() ? 0 : 1;

        if (DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $user_id, 'is_guest' => $user_type])->first()) {
            DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $user_id, 'is_guest' => $user_type])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_list(Request $request): JsonResponse
    {
        $orders = $this->order->where(['user_id' => $request->user()->id])->get();
        return response()->json($orders, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get_order_details(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = $this->order_detail->where(['order_id' => $request['order_id']])->get();
        foreach ($details as $det) {
            $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
        }

        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
       return response()->json($request->user(), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_profile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => ['required', 'unique:users,phone,'.auth()->user()->id]
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
            'phone.required' => 'Phone is required!',
            'phone.unique' => translate('Phone must be unique!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image = $request->file('image');

        if ($image != null) {
            $data = getimagesize($image);
            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('profile')) {
                Storage::disk('public')->makeDirectory('profile');
            }
            $note_img = Image::make($image)->fit($data[0], $data[1])->stream();
            Storage::disk('public')->put('profile/' . $imageName, $note_img);
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now()
        ];

        $this->user->where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update_cm_firebase_token(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        DB::table('users')->where('id',$request->user()->id)->update([
            'cm_firebase_token'=>$request['cm_firebase_token']
        ]);

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function subscribe_newsletter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $newsLetter = $this->newsletter->where('email', $request->email)->first();
        if (!isset($newsLetter)) {
            $newsLetter = $this->newsletter;
            $newsLetter->email = $request->email;
            $newsLetter->save();

            try {
                $emailServices = Helpers::get_business_settings('mail_config');
                if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                    Mail::to(Helpers::get_business_settings('email_address'))->send(new \App\Mail\Admin\SubscribeNewsletter($newsLetter->email));
                }
            } catch (\Exception $e) {
            }

            return response()->json(['message' => 'Successfully subscribed'], 200);

        } else {
            return response()->json(['message' => 'Email Already exists'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function remove_account(Request $request): JsonResponse
    {
        $customer = $this->user->find($request->user()->id);
        if(isset($customer)) {
            Helpers::file_remover('profile/', $customer->image);
            $customer->delete();

        } else {
            return response()->json(['status_code' => 404, 'message' => translate('Not found')], 200);
        }

        $conversations = $this->conversation->where('user_id', $customer->id)->get();
        foreach ($conversations as $conversation){
            if ($conversation->checked == 0){
                $conversation->checked = 1;
                $conversation->save();
            }
        }

        return response()->json(['status_code' => 200, 'message' => translate('Successfully deleted')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function change_language(Request $request): JsonResponse
    {
        if (auth('api')->user()){
            $customer = $this->user->find(auth('api')->user()->id);
            $customer->language_code = $request->language_code ?? 'en';
            $customer->save();
            return response()->json(200);
        }else{
            $guest = $this->guest_user::find($request->header('guest-id'));
            if (!isset($guest)) {
                $guest = $this->guest_user;
                $guest->ip_address = $request->ip();
                $guest->fcm_token = $request->fcm_token ?? null;
            }
            $guest->language_code = $request->language_code ?? 'en';
            $guest->save();
            return response()->json(200);
        }
    }

    /**
     * @return JsonResponse
     */
    public function last_ordered_address(): JsonResponse
    {
        if (!auth('api')->user()){
            return response()->json(['status_code' => 401, 'message' => translate('Unauthorized')], 200);
        }

        $user_id = auth('api')->user()->id;

        $order = $this->order->where(['user_id' => $user_id, 'is_guest' => 0])
            ->whereNotNull('delivery_address_id') // Use whereNotNull to filter not null delivery_address_id
            ->orderBy('id', 'DESC')
            ->with('delivery_address')
            ->first();

        if (isset($order) && $order->delivery_address){
            return response()->json($order->delivery_address, 200);
        }

        return response()->json(null, 200);

    }
}

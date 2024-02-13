<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Notification;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function __construct(
        private Notification $notification
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function index(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
           $notifications = $this->notification->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('title', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                        }
            });
            $query_param = ['search' => $request['search']];
        }else{
           $notifications = $this->notification;
        }
        $notifications = $notifications->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.notification.index', compact('notifications','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'image'=> 'required'
        ], [
            'title.required' => 'title is required!',
        ]);

        if ($request->has('image')) {
            $image_name = Helpers::upload('notification/', 'png', $request->file('image'));
        } else {
            $image_name = null;
        }

        $notification = $this->notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->status = 1;
        $notification->save();

        try {
            $notification->type = 'general';
            Helpers::send_push_notif_to_topic($notification);
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed!'));

        }

        Toastr::success(translate('Notification sent successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $notification = $this->notification->find($id);
        return view('admin-views.notification.edit', compact('notification'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:100',
            'description' => 'required|max:255',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = $this->notification->find($id);

        if ($request->has('image')) {
            $image_name = Helpers::update('notification/', $notification->image, 'png', $request->file('image'));
        } else {
            $image_name = $notification['image'];
        }

        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $image_name;
        $notification->save();
        Toastr::success(translate('Notification updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        $notification->status = $request->status;
        $notification->save();
        Toastr::success(translate('Notification status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        if (Storage::disk('public')->exists('notification/' . $notification['image'])) {
            Storage::disk('public')->delete('notification/' . $notification['image']);
        }
        $notification->delete();
        Toastr::success(translate('Notification removed!'));
        return back();
    }
}

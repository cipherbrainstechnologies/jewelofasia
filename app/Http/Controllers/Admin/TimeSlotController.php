<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\TimeSlot;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeSlotController extends Controller
{
    public function __construct(
        private TimeSlot $time_slot
    ){}

    /**
     * @return Application|Factory|View
     */
    public function add_new(): View|Factory|Application
    {
        $timeSlots = $this->time_slot->orderBy('start_time', 'asc')->get();
        return view('admin-views.timeSlot.index', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        $start_time = $request->start_time;
        $end_time = $request->end_time;
        //time overlap check
        $slots = $this->time_slot->latest()->get(['start_time', 'end_time']);

        foreach ($slots as $slot) {
            $exist_start = date('H:i', strtotime($slot->start_time));
            $exist_end = date('H:i', strtotime($slot->end_time));
            if(($start_time >= $exist_start && $start_time <= $exist_end) || ($end_time >= $exist_start && $end_time <= $exist_end)) {
                Toastr::error(translate('Time slot overlaps with existing timeslot...'));
                return back();
            }
            if(($exist_start >= $start_time && $exist_start <= $end_time) || ($exist_end >= $start_time && $exist_end <= $end_time)) {
                Toastr::error(translate('Time slot overlaps with existing timeslot!!!'));
                return back();
            }
        }

        DB::table('time_slots')->insert([
            'start_time' => $start_time,
            'end_time'   => $end_time,
            'date'       => date('Y-m-d'),
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Time Slot added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $timeSlots = $this->time_slot->where(['id' => $id])->first();
        return view('admin-views.timeSlot.edit', compact('timeSlots'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([

            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',

        ]);

        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $slots = $this->time_slot->where('id', '!=', $id)->get(['start_time', 'end_time']);

        foreach ($slots as $slot) {
            $exist_start = date('H:i', strtotime($slot->start_time));
            $exist_end = date('H:i', strtotime($slot->end_time));
            if(($start_time >= $exist_start && $start_time <= $exist_end) || ($end_time >= $exist_start && $end_time <= $exist_end)) {
                Toastr::error(translate('Time slot overlaps with existing timeslot...'));
                return back();
            }
            if(($exist_start >= $start_time && $exist_start <= $end_time) || ($exist_end >= $start_time && $exist_end <= $end_time)) {
                Toastr::error(translate('Time slot overlaps with existing timeslot!!!'));
                return back();
            }
        }

        DB::table('time_slots')->where(['id' => $id])->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'date'       => date('Y-m-d'),
            'status'     => 1,
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Time Slot updated successfully!'));
        return redirect()->route('admin.business-settings.store.timeSlot.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->time_slot->find($request->id);
        $timeSlot->status = $request->status;
        $timeSlot->save();
        Toastr::success(translate('TimeSlot status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $timeSlot = $this->time_slot->find($request->id);
        $timeSlot->delete();
        Toastr::success(translate('Time Slot removed!'));
        return back();
    }
}

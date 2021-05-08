<?php

namespace App\Http\Controllers;

use App\shift_schedule;
use App\schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftScheduleController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $pay_period = (object) $request->pay_period;
            $model = new shift_schedule;

            $model->name = $request->name;
            $model->pay_period_id = $pay_period->id;

            $model->save();

            $schedules = [];
            foreach ($request->item as $item) {
                $item = (object) $item;
                $temp = [
                    'shift_schedule_id' => $model->id,
                    'work_date' => $item->work_date,
                    'day' => $item->day,
                    'sched_in' => $item->shift_sched_in,
                    'sched_out' => $item->shift_sched_out,
                    'is_rest_day' => $item->is_rest_day,
                ];

                array_push($schedules, $temp);
            }

            schedule::insert($schedules);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Shift_Schedule: " . $schedules
            );

            DB::commit();
            return "ok";
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $this
                ->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "Error",
                    $ex->getMessage()
                );

            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );

            return response()->json(['error' => $ex], 500);
        }
    }

    public function show(shift_schedule $shift_schedule)
    {
        //
    }

    public function edit(shift_schedule $shift_schedule)
    {
        //
    }

    public function update(Request $request, shift_schedule $shift_schedule)
    {
        //
    }

    public function destroy(shift_schedule $shift_schedule)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\dtr;
use App\RALog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DtrController extends Controller
{
    public function index()
    {
        $tbl = dtr::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {


            $request = $request->toArray();
            foreach ($request as $item) {
                $item = (object) $item;
                $chk =  dtr::where("employee_id", $item->employee_id)
                    ->where("pay_period_id", $item->pay_period_id)
                    ->where("work_date", $item->work_date)
                    ->first();



                if (empty($chk)) {
                    DB::table('dtrs')->insert(
                        [
                            'employee_id' => $item->employee_id,
                            'pay_period_id' => $item->pay_period_id,
                            'work_date' => $item->work_date,
                            'day' => $item->day,
                            'shift_sched_in' => $item->shift_sched_in,
                            'shift_sched_out' => $item->shift_sched_out,
                            'is_rest_day' => $item->is_rest_day
                        ]
                    );
                } else {
                    DB::table('dtrs')
                        ->where("employee_id", $item->employee_id)
                        ->where("pay_period_id", $item->pay_period_id)
                        ->where("work_date", $item->work_date)
                        ->update(['shift_sched_in' => $item->shift_sched_in, 'shift_sched_out' => $item->shift_sched_out, 'is_rest_day' => $item->is_rest_day]);
                }
            }
            return "ok";
            // return $this->index();

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show(Request $request, $id)
    {
        return $request;
        $tbl = dtr::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(dtr $dtr)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = dtr::findOrFail($id);

            $input = $request->all();

            $cmd->fill($input)->save();

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            dtr::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function getDTR($period_id, $emp_id)
    {
        $tbl = dtr::where("employee_id", $emp_id)->where("pay_period_id", $period_id)->get();

        foreach ($tbl as $item) {

            if ($item->time_in == null) {
                $log = RALog::where(DB::raw("DATE(datetime)"), $item->work_date)
                    ->where("employeeID", $emp_id)
                    ->orderBy('datetime')
                    ->first();

                if ($log != null)
                    DB::table('dtrs')->where("id", $item->id)
                        ->update(['time_in' => $log->datetime]);
            }
            if ($item->time_out == null) {
                $log = RALog::where(DB::raw("DATE(datetime)"), $item->work_date)
                    ->where("employeeID", $emp_id)
                    ->orderBy('datetime', 'desc')
                    ->get();
                $c = count($log);
                if ($c > 1)
                    if ($log != null)
                        DB::table('dtrs')->where("id", $item->id)
                            ->update(['time_out' => $log[0]->datetime]);
            }
        }
        $tbl = dtr::where("employee_id", $emp_id)->where("pay_period_id", $period_id)->get();
        return response()->json($tbl);
    }

    public function getDTRinWorkDate($work_date, $emp_id)
    {
        $tbl = dtr::where("employee_id", $emp_id)->where("work_date", $work_date)->first();
        return response()->json($tbl);
    }

    public function getDTR_add(Request $request, $emp_id)
    {
        $date_from = new Carbon($request->from);
        $date_to = new Carbon($request->to);
        $daysCount = $date_from->diffInDays($date_to);

        $retVal = [];
        for ($x = 0; $x <= $daysCount; $x++) {
            $chk =  dtr::where("employee_id", $emp_id)
                ->where("pay_period_id", $request->id)
                ->first();

            $day = $date_from->englishDayOfWeek;
            $c1 = collect();
            $c1->put('index',  $x);
            $c1->put('employee_id',  $emp_id);
            $c1->put('pay_period_id', $request->id);
            $c1->put('work_date', $date_from->format('Y-m-d'));
            $c1->put('day', $day);


            if (!empty($chk)) {
                $tbl = dtr::where("employee_id", $emp_id)
                    ->where("pay_period_id", $request->id)
                    ->where("work_date", $date_from)
                    ->first();
                $c1->put('shift_sched_in', $tbl->shift_sched_in);
                $c1->put('shift_sched_out', $tbl->shift_sched_out);
                $c1->put('is_rest_day', $tbl->is_rest_day);
            } else {
                $c1->put('shift_sched_in', $date_from->format('Y-m-d') . " " . "08:30");
                if ($day == "Saturday") {
                    $c1->put('shift_sched_out', $date_from->format('Y-m-d') . " " . "12:00");
                } else {
                    $c1->put('shift_sched_out', $date_from->format('Y-m-d') . " " . "18:00");
                }
                if ($day == "Sunday") {
                    $c1->put('is_rest_day', true);
                } else {
                    $c1->put('is_rest_day', false);
                }
            }

            array_push($retVal, $c1);
            $date_from = $date_from->addDay();
        }

        return response()->json($retVal);
    }

    public function getDTRinRange($from, $to, $emp_id)
    {
        $tbl = dtr::where("employee_id", $emp_id)
            ->whereBetween("work_date", [$from, $to])
            ->get();

        $total_days = 0;
        $temp = [];
        foreach ($tbl as $item) {
            if ($item->is_rest_day == 0)
                $total_days++;
            $item->haftday = false;
            $item->haftday_type = 0;
            array_push($temp, $item);
            $item->time_in = $item->shift_sched_in;
            $item->time_out = $item->shift_sched_out;
        }
        $c1 = collect();
        $c1->put('item',  $temp);
        $c1->put('total_days',  $total_days);

        return response()->json($c1);
    }

    public function storeMultiple(Request $request)
    {
        try {
            $employees = $request->employees;
            $scheds = $request->scheds;
            foreach ($employees as $emp) {
                $emp = (object) $emp;
                if ($emp->chk) {
                    foreach ($scheds as $sched) {
                        $sched = (object) $sched;

                        $chk =  dtr::where("employee_id", $emp->id)
                            ->where("pay_period_id", $sched->pay_period_id)
                            ->where("work_date", $sched->work_date)
                            ->first();



                        if (empty($chk)) {
                            DB::table('dtrs')->insert(
                                [
                                    'employee_id' => $emp->id,
                                    'pay_period_id' => $sched->pay_period_id,
                                    'work_date' => $sched->work_date,
                                    'day' => $sched->day,
                                    'shift_sched_in' => $sched->shift_sched_in,
                                    'shift_sched_out' => $sched->shift_sched_out,
                                    'is_rest_day' => $sched->is_rest_day
                                ]
                            );
                        } else {
                            DB::table('dtrs')
                                ->where("employee_id", $emp->employee_id)
                                ->where("pay_period_id", $sched->pay_period_id)
                                ->where("work_date", $sched->work_date)
                                ->update(['shift_sched_in' => $sched->shift_sched_in, 'shift_sched_out' => $item->shift_sched_out, 'is_rest_day' => $item->is_rest_day]);
                        }
                    }
                }
            }
            return "ok";
            // return $this->index();

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\dtr;
use App\RALog;
use App\Employee;
use App\calendar_event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DtrController extends Controller
{
    private $cname = "DtrController";
    private $emp;

    public function __construct(Employee $emp)
    {
        $this->emp = $emp;
    }

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
            $r = $request;
            $request = $request->sched_items_add;
            foreach ($request as $item) {
                $item = (object) $item;
                $chk =  dtr::where("employee_id", $item->employee_id)
                    ->where("pay_period_id", $item->pay_period_id)
                    ->where("work_date", $item->work_date)
                    ->first();

                $timeIn = null;
                $timeOut = null;

                $log = RALog::where(DB::raw("DATE(datetime)"), $item->work_date)
                    ->where("employeeID", $item->employee_id)
                    ->orderBy('datetime')
                    ->first();
                if ($log != null)
                    $timeIn = $log->datetime;
                $log1 = RALog::where(DB::raw("DATE(datetime)"), $item->work_date)
                    ->where("employeeID", $item->employee_id)
                    ->orderBy('datetime', 'desc')
                    ->get();
                $c = count($log1);
                if ($c > 1)
                    if ($log1 != null)
                        $timeOut = $log1[0]->datetime;

                if (empty($chk)) {
                    $data = DB::table('dtrs')->insert(
                        [
                            'employee_id' => $item->employee_id,
                            'pay_period_id' => $item->pay_period_id,
                            'work_date' => $item->work_date,
                            'day' => $item->day,
                            'shift_sched_in' => $item->shift_sched_in,
                            'shift_sched_out' => $item->shift_sched_out,
                            'is_rest_day' => $item->is_rest_day,
                            'time_in' => $timeIn,
                            'time_out' => $timeOut
                        ]
                    );

                    \Logger::instance()->log(
                        Carbon::now(),
                        $r->user_id,
                        $r->user_name,
                        $this->cname,
                        "store",
                        "message",
                        "Create new Dtr: " . $data
                    );
                } else {
                    $logFrom = $chk;
                    $data = DB::table('dtrs')
                        ->where("employee_id", $item->employee_id)
                        ->where("pay_period_id", $item->pay_period_id)
                        ->where("work_date", $item->work_date)
                        ->update([
                            'shift_sched_in' => $item->shift_sched_in,
                            'shift_sched_out' => $item->shift_sched_out,
                            'is_rest_day' => $item->is_rest_day,
                            'time_in' => $timeIn,
                            'time_out' => $timeOut
                        ]);
                    $logTo = $data;

                    \Logger::instance()->log(
                        Carbon::now(),
                        $request->user_id,
                        $request->user_name,
                        $this->cname,
                        "update",
                        "message",
                        "update dtr id " . $chk->id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
                    );
                }
            }
            return "ok";
            // return $this->index();

        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $r->user_id,
                $r->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
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
            $logFrom = $cmd->replicate();
            $input = $request->all();

            $cmd->fill($input)->save();

            $logTo = $cmd;

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Dtr id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $tbl1 = dtr::findOrFail($id);
            dtr::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Dtr id " . $id .
                    "\nOld Dtr: " . $tbl1
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function getDTR($period_id, $emp_id, $user_id, $user_name)
    {
        $tbl = dtr::where("employee_id", $emp_id)->where("pay_period_id", $period_id)->get();
        $emp = Employee::where("id", $emp_id)->first();
        foreach ($tbl as $item) {
            $logFrom = dtr::findOrFail($item->id);
            $logTo = null;

            if ($item->time_in == null) {
                $log = RALog::where(DB::raw("DATE(datetime)"), $item->work_date)
                    ->where("employeeID", $emp_id)
                    ->orderBy('datetime')
                    ->first();

                if ($log != null)
                    $logTo = DB::table('dtrs')->where("id", $item->id)
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
                        $logTo = DB::table('dtrs')->where("id", $item->id)
                            ->update(['time_out' => $log[0]->datetime]);
            }

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "update",
                "message",
                "update client id " . $item->id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );
        }
        $tbl = dtr::where("employee_id", $emp_id)->where("pay_period_id", $period_id)->get();

        $retVal = [];
        if (count($tbl) > 0)
            foreach ($tbl as $dtr) {
                $no_in_and_out = 0;
                $no_in_or_out = 0;
                $naa = 0;
                $late = 0;
                $undertime = 0;
                $rd = 0;
                //holiday
                $checkIfHoliday = false;
                $calendar = [];
                if ($dtr->work_date != null)
                    $calendar = DB::table('calendar_events')
                        ->where('type', 'holiday')
                        ->where('frequency', 'This year only')
                        ->where('calendar_events.from', '<=', $dtr->work_date)
                        ->where('calendar_events.to', '>=', $dtr->work_date)
                        ->where('branch_id', '0')
                        ->orWhere('branch_id', $emp->branch_id)
                        ->where('type', 'holiday')
                        ->where('frequency', 'This year only')
                        ->where('calendar_events.from', '<=', $dtr->work_date)
                        ->where('calendar_events.to', '>=', $dtr->work_date)
                        ->get();
                if (count($calendar) > 0)
                    $checkIfHoliday = true;
                else {
                    $dtrCbn = new Carbon($dtr->work_date);
                    $dtrYear =  $dtrCbn->year;
                    if ($dtr->work_date != null)
                        // SELECT * FROM `calendar_events`
                        // WHERE type = 'holiday' and frequency = 'yearly'
                        // and  MONTH(calendar_events.from) <= MONTH('2020-06-12') and  MONTH(calendar_events.to) >= MONTH('2020-06-12')
                        // and  DAY(calendar_events.from) <= DAY('2020-06-12') and  DAY(calendar_events.to) >= DAY('2020-06-12')
                        $calendar1 = DB::table('calendar_events')
                            ->where('type', 'holiday')
                            ->where('frequency', 'Yearly')
                            ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                            ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                            ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                            ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                            ->where('branch_id', '0')
                            ->orWhere('branch_id', $emp->branch_id)
                            ->where('type', 'holiday')
                            ->where('frequency', 'Yearly')
                            ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                            ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                            ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                            ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                            ->get();
                    if (count($calendar1) > 0)
                        $checkIfHoliday = true;
                }
                //holiday
                if (!$checkIfHoliday)
                    if (!$dtr->is_rest_day)
                        if ($dtr->time_in != null || $dtr->time_out != null) {
                            if ($dtr->time_in != null && $dtr->time_out != null) {
                                $naa++;
                                $base_in = new Carbon($dtr->shift_sched_in);
                                $user_in = new Carbon($dtr->time_in);
                                $base_out = new Carbon($dtr->shift_sched_out);
                                $user_out = new Carbon($dtr->time_out);
                                if ($base_in < $user_in)
                                    $late += $base_in->diffInMinutes($user_in);
                                if ($base_out > $user_out)
                                    $undertime += $user_out->diffInMinutes($base_out);
                            } else
                                $no_in_or_out++;
                        } else
                            $no_in_and_out++;

                    else
                        $rd++;
                else {
                    unset($dtr->is_rest_day);
                    $dtr->is_rest_day = 3;
                }

                $c1 = collect();
                if ($naa > 0) {
                    $c1->put('late',  $late);
                    $c1->put('undertime',  $undertime);
                } else {
                    $c1->put('late',  '-');
                    $c1->put('undertime',  '-');
                }

                $c1->put('no_in_and_out',  $no_in_and_out);
                $c1->put('no_in_or_out', $no_in_or_out);
                // } else {
                //     $c1->put('naa',  '-');
                //     $c1->put('late',  '-');
                //     $c1->put('undertime',  '-');
                //     $c1->put('no_in_and_out',  '-');
                //     $c1->put('no_in_or_out', '-');
                // }
                $dtr->summary = $c1;
                //$c1->put('work_date', $date_from->format('Y-m-d'));
                array_push($retVal, $dtr);
            }
        return response()->json($retVal);
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
                $c1->put('shift_sched_in', $date_from->format('Y-m-d') . " " . "07:31");
                if ($day == "Saturday") {
                    $c1->put('shift_sched_out', $date_from->format('Y-m-d') . " " . "11:00");
                } else {
                    $c1->put('shift_sched_out', $date_from->format('Y-m-d') . " " . "17:00");
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

                foreach ($scheds as $sched) {
                    $sched = (object) $sched;

                    $chk =  dtr::where("employee_id", $emp->id)
                        ->where("pay_period_id", $sched->pay_period_id)
                        ->where("work_date", $sched->work_date)
                        ->first();

                    $timeIn = null;
                    $timeOut = null;

                    $log = RALog::where(DB::raw("DATE(datetime)"), $sched->work_date)
                        ->where("employeeID", $emp->id)
                        ->orderBy('datetime')
                        ->first();
                    if (!empty($log))
                        $timeIn = $log->datetime;

                    $log1 = RALog::where(DB::raw("DATE(datetime)"), $sched->work_date)
                        ->where("employeeID", $emp->id)
                        ->orderBy('datetime', 'desc')
                        ->get();
                    $c = count($log1);
                    if ($c > 1)
                        $timeOut = $log1[0]->datetime;

                    if (empty($chk)) {
                        $data = DB::table('dtrs')->insert(
                            [
                                'employee_id' => $emp->id,
                                'pay_period_id' => $sched->pay_period_id,
                                'work_date' => $sched->work_date,
                                'day' => $sched->day,
                                'shift_sched_in' => $sched->shift_sched_in,
                                'shift_sched_out' => $sched->shift_sched_out,
                                'is_rest_day' => $sched->is_rest_day,
                                'time_in' => $timeIn,
                                'time_out' => $timeOut
                            ]
                        );

                        \Logger::instance()->log(
                            Carbon::now(),
                            $request->user_id,
                            $request->user_name,
                            $this->cname,
                            "store",
                            "message",
                            "Create new Dtr: " . $data
                        );
                    } else {
                        $data = DB::table('dtrs')
                            ->where("employee_id", $emp->id)
                            ->where("pay_period_id", $sched->pay_period_id)
                            ->where("work_date", $sched->work_date);
                        $logFrom = $data->replicate()->first();
                        $logTo = $data->update([
                                'shift_sched_in' => $sched->shift_sched_in,
                                'shift_sched_out' => $sched->shift_sched_out,
                                'is_rest_day' => $sched->is_rest_day,
                                'time_in' => $timeIn,
                                'time_out' => $timeOut
                            ]);

                        \Logger::instance()->log(
                            Carbon::now(),
                            $request->user_id,
                            $request->user_name,
                            $this->cname,
                            "update",
                            "message",
                            "update dtr id " . $logFrom->id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
                        );
                    }
                }
            }
            return "ok";
            // return $this->index();

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function HRSummaryReport($period_id)
    {
        try {
            $tbl = Employee::with(['user', 'dtr' => function ($query) use ($period_id) {
                $query->where('pay_period_id', $period_id);
            }])->get();
            $retVal = [];
            foreach ($tbl as $item) {
                $dtrs = (object) $item->dtr;
                $no_in_and_out = 0;
                $no_in_or_out = 0;
                $late = 0;
                $undertime = 0;
                $naa = 0;
                if (count($dtrs) > 0)
                    foreach ($dtrs as $dtr) {
                        //holiday
                        $checkIfHoliday = false;
                        $calendar = [];
                        if ($dtr->work_date != null)
                            $calendar = DB::table('calendar_events')
                                ->where('type', 'holiday')
                                ->where('frequency', 'This year only')
                                ->where('calendar_events.from', '<=', $dtr->work_date)
                                ->where('calendar_events.to', '>=', $dtr->work_date)
                                ->where('branch_id', '0')
                                ->orWhere('branch_id', $item->branch_id)
                                ->where('type', 'holiday')
                                ->where('frequency', 'This year only')
                                ->where('calendar_events.from', '<=', $dtr->work_date)
                                ->where('calendar_events.to', '>=', $dtr->work_date)
                                ->get();
                        if (count($calendar) > 0)
                            $checkIfHoliday = true;
                        else {
                            $dtrCbn = new Carbon($dtr->work_date);
                            $dtrYear =  $dtrCbn->year;
                            if ($dtr->work_date != null)
                                // SELECT * FROM `calendar_events`
                                // WHERE type = 'holiday' and frequency = 'yearly'
                                // and  MONTH(calendar_events.from) <= MONTH('2020-06-12') and  MONTH(calendar_events.to) >= MONTH('2020-06-12')
                                // and  DAY(calendar_events.from) <= DAY('2020-06-12') and  DAY(calendar_events.to) >= DAY('2020-06-12')
                                $calendar1 = DB::table('calendar_events')
                                    ->where('type', 'holiday')
                                    ->where('frequency', 'Yearly')
                                    ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                                    ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                                    ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                                    ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                                    ->where('branch_id', '0')
                                    ->orWhere('branch_id', $item->branch_id)
                                    ->where('type', 'holiday')
                                    ->where('frequency', 'Yearly')
                                    ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                                    ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                                    ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                                    ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                                    ->get();
                            if (count($calendar1) > 0)
                                $checkIfHoliday = true;
                        }
                        //holiday
                        if (!$checkIfHoliday)
                            if (!$dtr->is_rest_day)
                                if ($dtr->time_in != null || $dtr->time_out != null) {
                                    if ($dtr->time_in != null && $dtr->time_out != null) {

                                        $naa++;
                                        $base_in = new Carbon($dtr->shift_sched_in);
                                        $user_in = new Carbon($dtr->time_in);
                                        $base_out = new Carbon($dtr->shift_sched_out);
                                        $user_out = new Carbon($dtr->time_out);
                                        if ($base_in < $user_in)
                                            $late += $base_in->diffInMinutes($user_in);
                                        if ($base_out > $user_out)
                                            $undertime += $user_out->diffInMinutes($base_out);
                                    } else
                                        $no_in_or_out++;
                                } else
                                    $no_in_and_out++;
                    }
                $c1 = collect();
                if (count($dtrs) > 0) {
                    if ($naa > 0) {
                        $c1->put('late',  $late);
                        $c1->put('undertime',  $undertime);
                    } else {
                        $c1->put('late',  '-');
                        $c1->put('undertime',  '-');
                    }

                    $c1->put('naa',  $naa);
                    $c1->put('no_in_and_out',  $no_in_and_out);
                    $c1->put('no_in_or_out', $no_in_or_out);
                } else {
                    $c1->put('naa',  '-');
                    $c1->put('late',  '-');
                    $c1->put('undertime',  '-');
                    $c1->put('no_in_and_out',  '-');
                    $c1->put('no_in_or_out', '-');
                }
                $item->summary = $c1;
                //$c1->put('work_date', $date_from->format('Y-m-d'));
                array_push($retVal, $item);
            }
            return $retVal;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function HRSummaryReport2(Request $request)
    {
        try {
            $start = $request->start;
            $end = $request->end;
            $tbl = Employee::with(['user', 'dtr' => function ($query) use ($start, $end) {
                $query->where('pay_period_id', $start);
                // $query->where('pay_period_id', $start); where between
            }])->get();
            $retVal = [];
            foreach ($tbl as $item) {
                $dtrs = (object) $item->dtr;
                $no_in_and_out = 0;
                $no_in_or_out = 0;
                $late = 0;
                $undertime = 0;
                $naa = 0;
                if (count($dtrs) > 0)
                    foreach ($dtrs as $dtr) {
                        //holiday
                        $checkIfHoliday = false;
                        $calendar = [];
                        if ($dtr->work_date != null)
                            $calendar = DB::table('calendar_events')
                                ->where('type', 'holiday')
                                ->where('frequency', 'This year only')
                                ->where('calendar_events.from', '<=', $dtr->work_date)
                                ->where('calendar_events.to', '>=', $dtr->work_date)
                                ->get();
                        if (count($calendar) > 0)
                            $checkIfHoliday = true;
                        else {
                            $dtrCbn = new Carbon($dtr->work_date);
                            $dtrYear =  $dtrCbn->year;
                            if ($dtr->work_date != null)
                                // SELECT * FROM `calendar_events`
                                // WHERE type = 'holiday' and frequency = 'yearly'
                                // and  MONTH(calendar_events.from) <= MONTH('2020-06-12') and  MONTH(calendar_events.to) >= MONTH('2020-06-12')
                                // and  DAY(calendar_events.from) <= DAY('2020-06-12') and  DAY(calendar_events.to) >= DAY('2020-06-12')
                                $calendar1 = DB::table('calendar_events')
                                    ->where('type', 'holiday')
                                    ->where('frequency', 'Yearly')
                                    ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                                    ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                                    ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                                    ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                                    ->get();
                            if (count($calendar1) > 0)
                                $checkIfHoliday = true;
                        }
                        //holiday
                        if (!$checkIfHoliday)
                            if (!$dtr->is_rest_day)
                                if ($dtr->time_in != null || $dtr->time_out != null) {
                                    if ($dtr->time_in != null && $dtr->time_out != null) {

                                        $naa++;
                                        $base_in = new Carbon($dtr->shift_sched_in);
                                        $user_in = new Carbon($dtr->time_in);
                                        $base_out = new Carbon($dtr->shift_sched_out);
                                        $user_out = new Carbon($dtr->time_out);
                                        if ($base_in < $user_in)
                                            $late += $base_in->diffInMinutes($user_in);
                                        if ($base_out > $user_out)
                                            $undertime += $user_out->diffInMinutes($base_out);
                                    } else
                                        $no_in_or_out++;
                                } else
                                    $no_in_and_out++;
                    }
                $c1 = collect();
                if (count($dtrs) > 0) {
                    if ($naa > 0) {
                        $c1->put('late',  $late);
                        $c1->put('undertime',  $undertime);
                    } else {
                        $c1->put('late',  '-');
                        $c1->put('undertime',  '-');
                    }

                    $c1->put('naa',  $naa);
                    $c1->put('no_in_and_out',  $no_in_and_out);
                    $c1->put('no_in_or_out', $no_in_or_out);
                } else {
                    $c1->put('naa',  '-');
                    $c1->put('late',  '-');
                    $c1->put('undertime',  '-');
                    $c1->put('no_in_and_out',  '-');
                    $c1->put('no_in_or_out', '-');
                }
                $item->summary = $c1;
                //$c1->put('work_date', $date_from->format('Y-m-d'));
                array_push($retVal, $item);
            }
            return $retVal;
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

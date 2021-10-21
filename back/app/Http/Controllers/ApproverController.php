<?php

namespace App\Http\Controllers;

use App\Approver;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Leave;
use App\over_time;
use App\official_business;
use App\change_shift;
use App\change_rest_day;
use App\missing_time_log;
use App\manual_attendance;
use stdClass;

class ApproverController extends Controller
{
    private $cname = "ApproverController";
    public function index()
    {
        $tbl = Approver::with(['employee'])->get();
        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $approver = Approver::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new approver with ID: " . $approver->id . "\nDetails: " . $approver
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = Approver::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(Approver $Approver)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = Approver::findOrFail($id);

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
            Approver::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function getApprover($emp_id, $type, $type_id)
    {
        try {

            $tbl = DB::table('employee_approvers')
                ->where('employee_id', $emp_id)
                ->orderBy('level')
                ->get();
            $ret_val = [];
            foreach ($tbl as $item) {
                $tbl = DB::table('approvers')
                    ->join('employees', 'employees.id', 'approvers.employee_id')
                    ->where('approvers.id', $item->approver_id)
                    ->first();
                $ttt = "";
                if ($type == "lv") {
                    $ttt = DB::table('leaves')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "ot") {
                    $ttt = DB::table('over_times')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "cs") {
                    $ttt = DB::table('change_shifts')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "crd") {
                    $ttt = DB::table('change_rest_days')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "ma") {
                    $ttt = DB::table('manual_attendances')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "mtl") {
                    $ttt = DB::table('missing_time_logs')
                        ->where('id', $type_id)
                        ->first();
                }
                if ($type == "ob") {
                    $ttt = DB::table('official_businesses')
                        ->where('id', $type_id)
                        ->first();
                }


                if ($item->level < $ttt->approve_level)
                    $item->status = "Approved";
                elseif ($item->level == $ttt->approve_level)
                    $item->status = $ttt->status;
                elseif ($ttt->approve_level == "0")
                    $item->status = "Approved";
                else
                    $item->status = "Pending";

                if ($ttt->approve_level == $item->level && $ttt->status == "Disapproved")
                    $item->remark = $ttt->remarks;
                $item->emp = $tbl;
                array_push($ret_val, $item);
            }

            return response()->json($ret_val);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function getToApprove($emp_id)
    {
        try {
            //leaves
            $tbl = Leave::with(['leave_type', 'leave_days', 'employee'])
                ->where('status', 'Pending')
                ->get();
            $ret_val = [];
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');
                //$item->employee = (object) $item->employee;
                //$fname = (array) $employee.first_name;
                $item->temp = $tbl;
                $item->description = $item->leave_type['name'];
                $item->first_name = $item->employee['first_name'];
                $item->last_name = $item->employee['last_name'];
                $item->work_date = "-";
                $item->from = (new Carbon($item->date_from))->toFormattedDateString();
                $item->to = (new Carbon($item->date_to))->toFormattedDateString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }
            //over_times
            $tbl = DB::table('over_times')
                ->join('employees', 'employees.id', 'over_times.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Overtime";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }
            //official_businesses
            $tbl = DB::table('official_businesses')
                ->join('employees', 'employees.id', 'official_businesses.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Official businesses";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }

            //change_shifts
            $tbl = DB::table('change_shifts')
                ->join('employees', 'employees.id', 'change_shifts.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Change shift";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }

            //change_rest_days
            $tbl = DB::table('change_rest_days')
                ->join('employees', 'employees.id', 'change_rest_days.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Change rest day";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                if ($item->type == 'Shift to Rest Day') {
                    $item->from = "-";
                    $item->to = "-";
                } else {
                    $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                    $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                }

                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }

            //missing_time_logs
            $tbl = DB::table('missing_time_logs')
                ->join('employees', 'employees.id', 'missing_time_logs.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Missing time logs";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }

            //manual_attendances
            $tbl = DB::table('manual_attendances')
                ->join('employees', 'employees.id', 'manual_attendances.employee_id')
                ->where('status', 'Pending')
                ->get();
            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->temp = $tbl;
                $item->description = "Manual attendance";
                $from = new Carbon($item->time_in);
                $to = new Carbon($item->time_out);
                $item->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $item->to = $to->toFormattedDateString() . " " . $to->toTimeString();
                $item->date_filed = (new Carbon($item->date_filed))->toFormattedDateString();
                if ($tbl != null)
                    if ($tbl->employee_id == $emp_id) {
                        array_push($ret_val, $item);
                    }
            }

            return response()->json($ret_val);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function approveRequest(Request $request)
    {

        //return $request;
        $emp_id = $request->employee_id;
        $level = DB::table('employee_approvers')
            ->where('employee_id', $emp_id)
            ->max('level');
        if (strpos($request->description, 'eave')) {
            //ok nani
            if ($level <= $request->approve_level) {
                //check balance
                $balance = DB::table('leave_balances')
                    ->where('employee_id', $emp_id)
                    ->where('leave_type_id', $request->leave_type_id)
                    ->sum('balance');
                $total_day = (float) $request->total_days;
                if ($balance >= $total_day) {
                    DB::table('leaves')
                        ->where('reference_no', $request->reference_no)
                        ->update(['status' => 'Approved', 'approve_level' => '0']);
                    //update dtr is_rest_day = 2
                    foreach ($request->leave_days as $item) {
                        if ($item['halfday'] == 0) {
                            DB::table('dtrs')
                                ->where('employee_id', $emp_id)
                                ->where('work_date', $item['leave_date'])
                                ->update(['is_rest_day' => '2']);
                        } else {
                            if ($item['halfday_type'] == 1) {
                                DB::table('dtrs')
                                    ->where('employee_id', $emp_id)
                                    ->where('work_date', $item['leave_date'])
                                    ->update(['shift_sched_in' => DB::raw('ADDTIME(shift_sched_in, "5:30:0.000000")')]); //
                            } else {
                                DB::table('dtrs')
                                    ->where('employee_id', $emp_id)
                                    ->where('work_date', $item['leave_date'])
                                    ->update(['shift_sched_out' => DB::raw('SUBTIME(shift_sched_out, "5:00:0.000000")')]); //
                            }
                        }
                    }
                    //update balance
                    $tbl = DB::table('leave_balances')
                        ->where('employee_id', $emp_id)
                        ->where('leave_type_id', $request->leave_type_id)
                        ->get();
                    $tempTotal = $total_day;
                    foreach ($tbl as $item) {
                        if ($tempTotal != 0) {
                            $count = 0;
                            if ($tempTotal >= $item->balance) {
                                $count = $item->balance;
                                $tempTotal -= $item->balance;
                            } else {
                                $count = $tempTotal;
                                $tempTotal = 0;
                            }

                            DB::table('leave_balances')
                                ->where('id', $item->id)
                                ->decrement('balance', $count);
                        }
                        //update balance end
                    }
                } else {
                    return response()->json(['error' => "Not enough Leave Balance"], 500);
                }
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('leaves')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description ==  'Overtime') {
            //ok na ning OT
            if ($level <= $request->approve_level) {
                DB::table('over_times')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('over_times')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description ==  'Official businesses') {
            // ok na ning OB
            if ($level <= $request->approve_level) {
                DB::table('official_businesses')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);
                DB::table('dtrs')
                    ->where('employee_id', $emp_id)
                    ->where('work_date', $request->work_date)
                    ->update(['time_in' => $request->time_in, 'time_out' => $request->time_out]);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('official_businesses')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description ==  'Change shift') {
            //ok
            if ($level <= $request->approve_level) {
                DB::table('change_shifts')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);

                $from = new Carbon($request->from);
                $to = new Carbon($request->to);

                DB::table('dtrs')
                    ->where('work_date', $request->work_date)
                    ->where('employee_id', $request->employee_id)
                    ->update(['shift_sched_in' => $from, 'shift_sched_out' => $to]);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('change_shifts')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description ==  'Change rest day') {
            //ok nana
            $from = new Carbon($request->from);
            $to = new Carbon($request->to);
            if ($level <= $request->approve_level) {
                DB::table('change_rest_days')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);

                if ($request->type == "Shift to Rest Day") {
                    DB::table('dtrs')
                        ->where('work_date', $request->work_date)
                        ->where('employee_id', $request->employee_id)
                        ->update(['is_rest_day' => '1']);
                } else {
                    DB::table('dtrs')
                        ->where('work_date', $request->work_date)
                        ->where('employee_id', $request->employee_id)
                        ->update(['is_rest_day' => '0', 'shift_sched_in' => $from, 'shift_sched_out' => $to]);
                }
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('change_rest_days')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description ==  'Missing time logs') {
            //ok nani pero dili dapat ing ani
            if ($level <= $request->approve_level) {
                DB::table('missing_time_logs')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);
                DB::table('dtrs')
                    ->where('employee_id', $emp_id)
                    ->where('work_date', $request->work_date)
                    ->update(['time_in' => $request->time_in, 'time_out' => $request->time_out]);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('missing_time_logs')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        } else if ($request->description == 'Manual attendance') {
            //ok nani
            if ($level <= $request->approve_level) {
                DB::table('manual_attendances')
                    ->where('reference_no', $request->reference_no)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);
                DB::table('dtrs')
                    ->where('employee_id', $emp_id)
                    ->where('work_date', $request->work_date)
                    ->update(['time_in' => $request->time_in, 'time_out' => $request->time_out]);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('manual_attendances')
                    ->where('reference_no', $request->reference_no)
                    ->update(['approve_level' => $lvl]);
            }
        }

        return $this->getToApprove($request->userID);
    }

    public function disapproveRequest(Request $request)
    {
        if (strpos($request->description, 'eave')) {
            DB::table('leaves')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'Overtime') {
            DB::table('over_times')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'Official businesses') {
            DB::table('official_businesses')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'Change shift') {
            DB::table('change_shifts')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'Change rest day') {
            DB::table('change_rest_days')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'Missing time logs') {
            DB::table('missing_time_logs')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description == 'Manual attendance') {
            DB::table('manual_attendances')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        }

        return $this->getToApprove($request->userID);
    }

    public function getMyApp($id)
    {
        try {
            //leaves
            $lv =  Leave::with(['leave_type', 'leave_days', 'employee'])
                ->where("employee_id", $id)
                ->get();
            //Leave::with(['leave_type'])->where("employee_id", $id)->get();
            $ret_val = [];
            foreach ($lv as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Leave(' . $item->leave_type['name'] . ')');
                $c->put('workdate', '-');
                $c->put('from', (new Carbon($item->date_from))->toDateString());
                $c->put('to', (new Carbon($item->date_to))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('total_days', $item->total_days);
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                $c->put('leave_days', $item->leave_days);
                array_push($ret_val, $c);
            }

            //over_times
            $ot = over_time::where("employee_id", $id)->get();
            foreach ($ot as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Overtime');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                $c->put('with_break', $item->with_break);
                $c->put('break_hours', $item->break_hours);
                $c->put('total_hours', $item->total_hours);
                array_push($ret_val, $c);
            }
            //official_businesses
            $ob = official_business::where("employee_id", $id)->get();
            foreach ($ob as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Official Business');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                array_push($ret_val, $c);
            }
            //change_shifts
            $cs = change_shift::where("employee_id", $id)->get();
            foreach ($cs as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Change Shift');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                array_push($ret_val, $c);
            }
            //change_rest_days
            $cd = change_rest_day::where("employee_id", $id)->get();
            foreach ($cd as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Change Rest Day');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                $c->put('shift', $item->shift);
                $c->put('type', $item->type);
                array_push($ret_val, $c);
            }
            //missing_time_logs
            $mt = missing_time_log::where("employee_id", $id)->get();
            foreach ($mt as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Missing Time Log');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                array_push($ret_val, $c);
            }
            //manual_attendances
            $ma = manual_attendance::where("employee_id", $id)->get();
            foreach ($ma as $item) {
                $c = collect();
                $c->put('id', $item->id);
                $c->put('reference_no', $item->reference_no);
                $c->put('desc', 'Manual Attendance');
                $c->put('workdate',  $item->work_date);
                $c->put('from', (new Carbon($item->time_in))->toDateString());
                $c->put('to', (new Carbon($item->time_out))->toDateString());

                $c->put('date_filed', (new Carbon($item->date_filed))->toDateString());
                $c->put('reason', $item->reason);
                $c->put('status', $item->status);
                array_push($ret_val, $c);
            }

            return response()->json($ret_val);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

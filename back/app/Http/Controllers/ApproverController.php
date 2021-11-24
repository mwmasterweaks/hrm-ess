<?php

namespace App\Http\Controllers;

use App\Approver;
use App\biometric_attendance;
use App\dtr;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Leave;
use App\over_time;
use App\official_business;
use App\change_shift;
use App\change_rest_day;
use App\leave_day;
use App\leave_type;
use App\missing_time_log;
use App\manual_attendance;
use LeaveDay;
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

            // biometric_attendances
            $tbl = DB::table('biometric_attendances')
                ->join('employees', 'employees.id', 'biometric_attendances.employee_id')
                ->where('status', 'Pending')
                ->select('biometric_attendances.id AS bid', 'employees.*', 'biometric_attendances.*')
                ->get();

            foreach ($tbl as $item) {
                $tbl = DB::table('employee_approvers')
                    ->join('approvers', 'employee_approvers.approver_id', 'approvers.id')
                    ->where('employee_approvers.employee_id', $item->employee_id)
                    ->where('employee_approvers.level', $item->approve_level)
                    ->first('approvers.*');

                $item->reference_no = "BA-" . $item->bid;
                $item->temp = $tbl;
                $item->description = "Biometric attendance";
                $item->from = "--";
                $item->to = "--";
                // to review date format
                $item->punch_time_converted = Carbon::parse($item->punch_time, 'UTC')->isoFormat('lll');
                $item->date_filed = (new Carbon(substr($item->created_at, 0, 10)))->toFormattedDateString();
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
        } else if ($request->description == 'Overtime') {
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
        } else if ($request->description == 'Official businesses') {
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
        } else if ($request->description == 'Change shift') {
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
        } else if ($request->description == 'Change rest day') {
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
        } else if ($request->description == 'Missing time logs') {
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
        } else if ($request->description == 'Biometric attendance') {
            if ($level <= $request->approve_level) {
                DB::table('biometric_attendances')
                    ->where('id', $request->bid)
                    ->update(['status' => 'Approved', 'approve_level' => '0']);

                $type = 'time_out';
                if ($request->type == 'Time In') $type = 'time_in';
                DB::table('dtrs')
                    ->where('employee_id', $emp_id)
                    ->where('work_date', $request->work_date)
                    ->update([$type => $request->punch_time]);
            } else {
                $lvl = $request->approve_level + 1;
                DB::table('biometric_attendances')
                    ->where('id', $request->bid)
                    ->update(['approve_level' => $lvl]);
            }
        }

        return $this->getToApprove($request->userID);
    }

    public function publicApproveRequest($r, $t, $id, $eid)
    {
        if (is_numeric($id) && is_numeric($eid)) {
            $review = $r == 1 ? "Request Approved" : "Request Disapproved";
            $level = DB::table('employee_approvers')
                ->where('employee_id', $eid)
                ->max('level');
            $emp = DB::table('employees')->where('id', $eid)->first();
            if ($t == 1) { // not sure, needs testing
                $query = Leave::where('id', $id);
                $tbl =  (clone $query)->first();
                if ($r == 1) {
                    $leave_days = leave_day::where('leave_id', $id)->get();
                    if ($level <= $tbl->approve_level) {
                        //check balance
                        $balance = DB::table('leave_balances')
                            ->where('employee_id', $eid)
                            ->where('leave_type_id', $tbl->leave_type_id)
                            ->sum('balance');
                        $leave_type = leave_type::where('id', $tbl->leave_type_id)->first();
                        $total_day = (float) $tbl->total_days;
                        if ($balance >= $total_day) {
                            $log_to = tap(clone $query)->update([
                                'status' => 'Approved',
                                'approve_level' => '0'
                            ])->first();
                            //update dtr is_rest_day = 2
                            foreach ($leave_days as $item) {
                                $item = (object) $item;
                                $dtr_query = dtr::where('employee_id', $eid)
                                    ->where('work_date', $tbl->leave_date);
                                if ($item->halfday == 0) {
                                    $dtr_log = tap($dtr_query)->update([
                                        'is_rest_day' => '2'
                                    ])->first();
                                } else {
                                    if ($item->halfday_type == 1) {
                                        $dtr_logs = tap(clone $query)->update([
                                            'shift_sched_in' => DB::raw('ADDTIME(shift_sched_in, "5:30:0.000000")')
                                        ])->first();
                                    } else {
                                        $dtr_logs = tap(clone $query)->update([
                                            'shift_sched_out' => DB::raw('SUBTIME(shift_sched_out, "5:00:0.000000")')
                                        ])->first();
                                    }
                                }
                            }
                            //update balance
                            $tbl2 = DB::table('leave_balances')
                                ->where('employee_id', $eid)
                                ->where('leave_type_id', $tbl->leave_type_id)
                                ->get();
                            $tempTotal = $total_day;
                            foreach ($tbl2 as $item) {
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
                            return $this->confMesssage(
                                "Request Disapproved (Not enough leave balance)",
                                "LV-" . $id,
                                "Official Business",
                                "-",
                                $emp->first_name . " " . $emp->last_name,
                                $tbl->from,
                                $tbl->to,
                                $tbl->reason,
                                "",
                                "",
                                "",
                                "",
                                $tbl->date_filed,
                                $tbl->total_days,
                                "",
                                "",
                                "",
                                "",
                                ""
                            );
                        }
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        DB::table('leaves')
                            ->where('reference_no', $tbl->reference_no)
                            ->update(['approve_level' => $lvl]);
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->date_from);
                $to = new Carbon($tbl->date_to);
                $tbl->from = $from->toFormattedDateString();
                $tbl->to = $to->toFormattedDateString();

                return $this->confMesssage(
                    $review,
                    "LV-" . $id,
                    "Official Business",
                    "-",
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    $tbl->date_filed,
                    $tbl->total_days,
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
            if ($t == 2) {
                $query = over_time::where('id', $id);
                $tbl = (clone $query)->first();
                if ($r == 1) {
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->time_in);
                $to = new Carbon($tbl->time_out);
                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "OT-" . $id,
                    "Overtime",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    $tbl->date_filed,
                    "",
                    $tbl->with_break,
                    $tbl->break_hours,
                    $tbl->total_hours,
                    "",
                    ""
                );
            }
            if ($t == 3) {
                $query = official_business::where('id', $id);
                $tbl = (clone $query)->first();
                if ($r == 1) {
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();
                        $dtr_query = dtr::where('employee_id', $eid)
                            ->where('work_date', $tbl->work_date);
                        $dtr_log = tap($dtr_query)->update([
                            'time_in' => $tbl->time_in,
                            'time_out' => $tbl->time_out
                        ])->first();
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->time_in);
                $to = new Carbon($tbl->time_out);
                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "OB-" . $id,
                    "Official Business",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
            if ($t == 4) {
                $query = change_shift::where('id', $id);
                $tbl = (clone $query)->first();
                if ($r == 1) {
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();
                        $from = new Carbon($tbl->from);
                        $to = new Carbon($tbl->to);
                        $dtr_query = dtr::where('employee_id', $eid)
                            ->where('work_date', $tbl->work_date);
                        $dtr_log = tap($dtr_query)->update([
                            'shift_sched_in' => $from,
                            'shift_sched_out' => $to
                        ])->first();
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->time_in);
                $to = new Carbon($tbl->time_out);
                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "CS-" . $id,
                    "Change Shift",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
            if ($t == 5) {
                $query = change_rest_day::where('id', $id);
                $tbl = (clone $query)->first();
                if ($r == 1) {
                    $from = new Carbon($tbl->time_in);
                    $to = new Carbon($tbl->time_out);
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();

                        $dtr_query = dtr::where('employee_id', $eid)
                            ->where('work_date', $tbl->work_date);
                        if ($tbl->type == "Shift to Rest Day") {
                            $dtr_log = tap(clone $dtr_query)->update([
                                'is_rest_day' => '1'
                            ])->first();
                        } else {
                            $dtr_log = tap(clone $dtr_query)->update([
                                'is_rest_day' => '0',
                                'shift_sched_in' => $from,
                                'shift_sched_out' => $to
                            ])->first();
                        }
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "CRD-" . $id,
                    "Change Rest Day",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    $tbl->shift,
                    $tbl->type
                );
            }
            if ($t == 6) {
                $query = missing_time_log::where('id', $id);
                $tbl = (clone $query)->first();
                if ($r == 1) {
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();
                        $dtr_query = dtr::where('employee_id', $eid)
                            ->where('work_date', $tbl->work_date);
                        $dtr_log = tap($dtr_query)->update([
                            'time_in' => $tbl->time_in,
                            'time_out' => $tbl->time_out
                        ])->first();
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->time_in);
                $to = new Carbon($tbl->time_out);
                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "MTL-" . $id,
                    "Missing Time Logs",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
            if ($t == 7) {
                $query = manual_attendance::where('id', $id);
                $tbl =  (clone $query)->first();
                if ($r == 1) {
                    if ($level <= $tbl->approve_level) {
                        $log_to = tap(clone $query)->update([
                            'status' => 'Approved',
                            'approve_level' => '0'
                        ])->first();
                        $dtr_query = dtr::where('employee_id', $eid)
                            ->where('work_date', $tbl->work_date);
                        $dtr_log = tap($dtr_query)->update([
                            'time_in' => $tbl->time_in,
                            'time_out' => $tbl->time_out
                        ])->first();
                    } else {
                        $lvl = $tbl->approve_level + 1;
                        $log_to = tap(clone $query)->update([
                            'approve_level' => $lvl
                        ])->first();
                    }
                } else {
                    $log_to = tap(clone $query)->update([
                        'remarks' => $tbl->remarks,
                        'status' => 'Disapproved'
                    ])->first();
                }

                $from = new Carbon($tbl->time_in);
                $to = new Carbon($tbl->time_out);
                $tbl->from = $from->toFormattedDateString() . " " . $from->toTimeString();
                $tbl->to = $to->toFormattedDateString() . " " . $to->toTimeString();

                return $this->confMesssage(
                    $review,
                    "MA-" . $id,
                    "Manual Attendance",
                    $tbl->work_date,
                    $emp->first_name . " " . $emp->last_name,
                    $tbl->from,
                    $tbl->to,
                    $tbl->reason,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
            if ($t == 8) {
                $query = biometric_attendance::where('id', $id);
                $tbl = (clone $query)->first();
                if ($tbl->approve_level != 0) {
                    if ($r == 1) {

                        if ($level <= $tbl->approve_level) {
                            $log_to = biometric_attendance::where('id', $id)->update([
                                'status' => 'Approved',
                                'approve_level' => '0'
                            ]);

                            $type = 'time_out';
                            $workdate = substr($tbl->punch_time, 0, 10);
                            if ($tbl->type == 'Time In')
                                $type = 'time_in';
                            //
                            dtr::where('employee_id', $eid)
                                ->where('work_date', $workdate)
                                ->update([
                                    $type => $tbl->punch_time
                                ]);
                        } else {
                            $lvl = $tbl->approve_level + 1;
                            $log_to = biometric_attendance::where('id', $id)->update([
                                'approve_level' => $lvl
                            ]);
                        }
                    } else {
                        $log_to = biometric_attendance::where('id', $id)->update([
                            'status' => 'Disapproved'
                        ]);
                    }
                }
                return $this->confMesssage(
                    $review,
                    "BA-" . $id,
                    "Biometric Attendance",
                    "",
                    $emp->first_name . " " . $emp->last_name,
                    "",
                    "",
                    "",
                    $tbl->type,
                    $tbl->latitude,
                    $tbl->longitude,
                    $tbl->punch_time,
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                    ""
                );
            }
        } else
            return 0;
    }

    public function disapproveRequest(Request $request)
    {
        if (strpos($request->description, 'eave')) {
            DB::table('leaves')
                ->where('reference_no', $request->reference_no)
                ->update(['remarks' => $request->remarks, 'status' => 'Disapproved']);
        } else if ($request->description ==  'O
        vertime') {
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
        } else if ($request->description == 'Biometric attendance') {
            DB::table('biometric_attendances')
                ->where('id', $request->bid)
                ->update(['status' => 'Disapproved']);
        }

        return $this->getToApprove($request->userID);
    }

    public function confMesssage(
        $review,
        $ref,
        $desc,
        $work_date,
        $emp_name,
        $from,
        $to,
        $reason,
        $ba_type,
        $lat,
        $lng,
        $punch_time,
        $date_filed,
        $total_days,
        $w_break,
        $break_hours,
        $total_hours,
        $shift,
        $crd_type
    ) {
        $ret =
            "<div id='container'>
            <div class='main'>
                <span class='common head'>
                    HRMESS
                </span>";

        if ($review == "Request Approved") {
            $ret .=
                "<span class='common message'>" .
                $review
                . "</span>";
        } else {
            $ret .=
                "<span class='common message' style='color: #c03434'>" .
                $review
                . "</span>";
        }
        $ret .=
            "<span class='common details'>
                    <br/>
                    <table class='my-table'>
                        <tr>
                            <td class='my-td'>Reference no:</td>
                            <td class='my-td'>" . $ref . "</td>

                            <td class='my-td'>Description:</td>
                            <td class='my-td'>" . $desc . "</td>
                        </tr>";

        if (substr($ref, 0, 2) == 'BA') {
            $ret .=
                "<tr>
                            <td class='my-td'>Type</td>
                            <td class='my-td'>" . $ba_type . "</td>

                            <td class='my-td'>Employee:</td>
                            <td class='my-td'>" . $emp_name . "</td>
                        </tr>

                        <tr>
                            <td class='my-td'>Latitude:</td>
                            <td class='my-td'>" . $lat . "</td>

                            <td class='my-td'>Longitude:</td>
                            <td class='my-td'>" . $lng . "</td>
                        </tr>

                        <tr>
                            <td class='my-td'>Punch Time:</td>
                            <td class='my-td' colspan='3'>" . $punch_time . "</td>
                        </tr>";
        } else {
            $ret .=
                "<tr>
                            <td class='my-td'>Work Date:</td>
                            <td class='my-td'>" . $work_date . "</td>

                            <td class='my-td'>Employee:</td>
                            <td class='my-td'>" . $emp_name . "</td>
                        </tr>";


            if (substr($ref, 0, 2) == 'CR') {
                $ret .=
                    "<tr>
                            <td class='my-td'>Shift:</td>
                            <td class='my-td'>" . $shift . "</td>

                            <td class='my-td'>Type:</td>
                            <td class='my-td'>" . $crd_type . "</td>
                        </tr>";
            }

            $ret .=
                "<tr>
                            <td class='my-td'>From:</td>
                            <td class='my-td'>" . $from . "</td>

                            <td class='my-td'>To:</td>
                            <td class='my-td'>" . $to . "</td>
                        </tr>";

            if (substr($ref, 0, 2) == 'LV') {
                $ret .=
                    "<tr>
                            <td class='my-td'>Date Filed:</td>
                            <td class='my-td'>" . $date_filed . "</td>

                            <td class='my-td'>Total day/s:</td>
                            <td class='my-td'>" . $total_days . "</td>
                        </tr>";
            }

            if (substr($ref, 0, 2) == 'OT') {
                $ret .=
                    "<tr>
                            <td class='my-td'>With Break:</td>
                            <td class='my-td'>" . $w_break . "</td>

                            <td class='my-td'>Break Hours:</td>
                            <td class='my-td'>" . $break_hours . "</td>
                        </tr>

                        <tr>
                            <td class='my-td'>Date Filed:</td>
                            <td class='my-td'>" . $date_filed . "</td>

                            <td class='my-td'>Total Hours:</td>
                            <td class='my-td'>" . $total_hours . "</td>
                        </tr>";
            }

            $ret .=
                "<tr>
                            <td class='my-td'>Reason:</td>
                            <td class='my-td' colspan='3'>" . $reason . "</td>
                        </tr>";
        }

        $ret .=
            "</table>
                </span>
            </div>
        </div>

        <style>
            #container {
                display: flex;
                justify-content: center;
            }
            .main {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: linear-gradient(to bottom, #008015 0%, #008015 95px, #f7f7f7 95px, #f7f7f7 100%);
                margin-top: 80px;
                padding: 40px;
                padding-top: 29px;
                border-radius: 6px;
                text-align: center;
            }
            .common {
                font-family: 'Calibri';
                font-weight: bold;
            }
            .head {
                color: #ffffff;
                font-size: 30px;
                margin-bottom: 20px;
                align-self: center;
                letter-spacing: 3px;
            }
            .message {
                font-size: 20px;
                color: #008015;
                letter-spacing: 0.5px;
                align-self: start;
                padding-top: 30px;
            }
            .details {
                color: #4e4e4e;
                font-size: medium;
                align-self: start
            }
            .my-td {
                padding: 10px;
            }
            .my-table,
            .my-td {
                border: 1px solid #b6b6b6;
            }
            .my-table {
                border-collapse: collapse;
                width: 100%;
            }
        </style>";

        return $ret;
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

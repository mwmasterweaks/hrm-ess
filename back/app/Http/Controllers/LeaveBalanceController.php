<?php

namespace App\Http\Controllers;

use App\Leave;
use App\leave_balance;
use App\leave_update_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveBalanceController extends Controller
{
    private $cname = "LeaveBalanceController";
    public function index()
    {
        $tbl = leave_balance::with(['leave_type'])->get();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            if ($request->multiple == 1) {
                $data = "";

                foreach ($request->employees as $item) {
                    $item = (object) $item;
                    $row = leave_balance::where('employee_id', $item->id)
                        ->where('leave_type_id', $request->leave_type_id);
                    $row_match = (clone $row)->count();

                    if ($row_match > 0) {
                        if ($request->action == 'update') {
                            $logFrom = (clone $row)->first();
                            $logTo = tap((clone $row)->first(), function ($row) use ($request) {
                                $row->balance = $request->balance;
                                $row->availed = $request->availed;
                                $row->accrued = $request->accrued;
                                $row->save();
                            });

                            $lb_logTo = leave_update_history::create([
                                'old_details' =>
                                    'Type: ' . $request->leave_type_id .
                                    ' Balance: ' . $logFrom->balance .
                                    ' Availed: ' . $logFrom->availed .
                                    ' Accrued: ' . $logFrom->accrued,
                                'updated_details' =>
                                    'Type: ' . $request->leave_type_id .
                                    ' Balance: ' . $request->balance .
                                    ' Availed: ' . $request->availed .
                                    ' Accrued: ' . $request->accrued,
                                'user_id' => $request->user_id,
                                'employee_id' => $item->id
                            ]);

                            $data .= "\nUpdate leave balance with ID: " . $logTo->id .
                                "\nFrom: " . $logFrom . "\nTo: " . $logTo;
                        }
                    } else {
                        if ($request->action == 'add') {
                            $lb = new leave_balance;
                            $lb->employee_id = $item->id;
                            $lb->leave_type_id = $request->leave_type_id;
                            $lb->enroll_year = $request->enroll_year;
                            $lb->balance = $request->balance;
                            $lb->availed = $request->availed;
                            $lb->accrued = $request->accrued;

                            $lb->save();

                            $lb_logTo = leave_update_history::create([
                                'old_details' => '',
                                'updated_details' =>
                                    'Type: ' . $request->leave_type_id .
                                    ' Balance: ' . $request->balance .
                                    ' Availed: ' . $request->availed .
                                    ' Accrued: ' . $request->accrued,
                                'user_id' => $request->user_id,
                                'employee_id' => $item->id
                            ]);

                            $data .= "\nCreate new leave balance with ID: " . $lb->id . "\nDetails: " .  $lb;
                        }
                    }
                }

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    $data
                );

                return 0;
            } else {
                $lb = leave_balance::create($request->all());

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    "Create new leave balance with ID: " . $lb->id . "\nDetails: " .  $lb
                );

                return $this->show($request->employee_id);
            }
            return 0;
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->employee_id,
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
        $tbl = leave_balance::with(['leave_type'])->where("employee_id", $id)->get();

        return response()->json($tbl);
    }
    public function edit(leave_balance $leave_balance)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {
            $leave_type = (object) $request->leave_type;
            $cmd  = leave_balance::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();
            $cmd->fill($input)->save();
            $logTo = $cmd;

            $lb_logTo = leave_update_history::create([
                'old_details' =>
                    'Type: ' . $leave_type->name .
                    ', Balance: ' . $logFrom->balance .
                    ', Availed: ' . $logFrom->availed .
                    ', Accrued: ' . $logFrom->accrued,
                'updated_details' =>
                    'Type: ' . $leave_type->name .
                    ', Balance: ' . $request->balance .
                    ', Availed: ' . $request->availed .
                    ', Accrued: ' . $request->accrued,
                'user_id' => $request->user_id,
                'employee_id' => $request->employee_id
            ]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "Update leave balance with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return $this->show($request->employee_id);
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
        $value = explode(",", $id);
        $lb_id = $value[0];
        $user_id = $value[1];
        $user_name = $value[2];
        try {
            // lb_id, user_id, user_name

            $lb = leave_balance::where('id', $lb_id);
            $emp_id = (clone $lb)->value('employee_id');
            $deleted = (clone $lb)->first();
            leave_balance::destroy($lb_id);

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "message",
                "Delete leave balance with ID: " . $lb_id .
                    "\nDetails: " . $deleted
            );

            return $this->show($emp_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function getBalance($emp_id, $leave_type_id)
    {
        $tbl = DB::table('leave_balances')
            ->where("employee_id", $emp_id)
            ->where("leave_type_id", $leave_type_id)
            ->sum("balance");
        if ($leave_type_id == 1) {
            return 30;
        } else
            return response()->json($tbl);
    }
}

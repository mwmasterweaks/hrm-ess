<?php

namespace App\Http\Controllers;

use App\Leave;
use App\leave_balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

                foreach ($request->employees as $item) {
                    $item = (object) $item;
                    $lb = new leave_balance;
                    $lb->employee_id = $item->id;
                    $lb->leave_type_id = $request->leave_type_id;
                    $lb->enroll_year = $request->enroll_year;
                    $lb->balance = $request->balance;
                    $lb->availed = $request->availed;
                    $lb->accrued = $request->accrued;

                    $lb->save();

                    \Logger::instance()->log(
                        Carbon::now(),
                        $request->user_id,
                        $request->user_name,
                        $this->cname,
                        "store",
                        "message",
                        "Create new Leave_Balance: " . $lb
                    );
                }
            return 0;
            } else {
                $data = leave_balance::create($request->all());

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    "Create new Branch: " . $data
                );
                return $this->show($request->employee_id);
            }
            return 0;

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
            $logFrom  = leave_balance::findOrFail($id);
            $logTo = leave_balance::where("id", $id)
                ->update([
                    "enroll_year" => $request->enroll_year, "balance" => $request->balance,
                    "availed" => $request->availed, "accrued" => $request->accrued
                ]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "update Leave_Balance id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
        try {
            $tbl1 = leave_balance::findOrFail($id);
            leave_balance::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Leave_Balance id " . $id .
                    "\nOld Leave_Balance: " . $tbl1
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

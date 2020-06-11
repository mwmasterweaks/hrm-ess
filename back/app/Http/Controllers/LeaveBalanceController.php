<?php

namespace App\Http\Controllers;

use App\Leave;
use App\leave_balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveBalanceController extends Controller
{
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

                    if ($item->chk) {
                        $lb = new leave_balance;

                        $lb->employee_id = $item->id;
                        $lb->leave_type_id = $request->leave_type_id;
                        $lb->enroll_year = $request->enroll_year;
                        $lb->balance = $request->balance;
                        $lb->availed = $request->availed;
                        $lb->accrued = $request->accrued;

                        $lb->save();
                    }
                }
                return 0;
            } else {
                leave_balance::create($request->all());
                return $this->show($request->employee_id);
            }
            return 0;
        } catch (\Exception $ex) {
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

            leave_balance::where("id", $id)
                ->update([
                    "enroll_year" => $request->enroll_year, "balance" => $request->balance,
                    "availed" => $request->availed, "accrued" => $request->accrued
                ]);
            // $cmd  = leave_balance::findOrFail($id);

            // $input = $request->all();

            // $cmd->fill($input)->save();

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            leave_balance::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
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

<?php

namespace App\Http\Controllers;

use App\Employee_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Approver;
use Carbon\Carbon;

class EmployeeApproverController extends Controller
{
    private $cname = "EmployeeApproverController";
    public function index()
    {
        $tbl = Employee_approver::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $tbl = DB::table('employee_approvers')
                ->where("employee_id", $request->employee_id)
                ->max("level");

            $level = (int) ($tbl + 1);
            //return $level;
            $tblInserted = Employee_approver::create($request->except('level') + ["level" => $level]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new employee_approver with approver ID and employee ID: " .
                    $tblInserted->approver_id . ", " . $request->employee_id .
                    "\nDetails: " .  $tblInserted
            );

            return $this->show($request->employee_id);
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
        $tbl = Employee_approver::where("employee_id", $id)->get();

        $retVal = [];
        foreach ($tbl as $item) {
            $app = Approver::with(['employee'])
                ->where("id", $item->approver_id)
                ->first();
            $item->app = $app;

            array_push($retVal, $item);
        }
        return response()->json($retVal);
    }


    public function edit(Employee_approver $Employee_approver)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $value = explode(",", $id);
        try {
            // $cmd  = Employee_approver::findOrFail($id);
            // $input = $request->all();
            // $cmd->fill($input)->save();

            $ea = Employee_approver::where('approver_id', $request->approver_id)
                ->where('employee_id', $request->employee_id);
            $logFrom = (clone $ea)->first();

            $emp_approver = tap((clone $ea))->update([
                'level' => $request->level
            ])->first();

            \Logger::instance()->log(
                Carbon::now(),
                $value[0],
                $value[1],
                $this->cname,
                "update",
                "message",
                "Update employee_approver with ID: " . $emp_approver->approver_id .
                    "\nFrom: " .  $logFrom . "\nTo: " . $emp_approver
            );

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $value[0],
                $value[1],
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
        $approver_id = $value[0];
        $employee_id = $value[1];
        try {
            // Employee_approver::destroy($id);

            $ea = Employee_approver::where('approver_id', $approver_id)
                ->where('employee_id', $employee_id);
            $deleted = (clone $ea)->first();
            (clone $ea)->delete();

            \Logger::instance()->log(
                Carbon::now(),
                $value[2],
                $value[3],
                $this->cname,
                "destroy",
                "message",
                "Delete employee_approver with approver ID and employee ID: " .
                    $approver_id . ", " . $employee_id .
                    "\nDetails: " . $deleted
            );

            return $this->show($employee_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $value[2],
                $value[3],
                $this->cname,
                "update",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function storeMultiple(Request $request)
    {
        try {
            $employees = $request->employees;
            $approve_add = (object) $request->approve_add;
            $data = [];
            $logData = "";

            foreach ($employees as $emp) {
                $emp = (object) $emp;
                $tbl = DB::table('employee_approvers')
                    ->where("employee_id", $request->employee_id)
                    ->max("level");

                $level = (int) ($tbl + 1);

                $temp = [
                    "approver_id" => $approve_add->approver_id,
                    "employee_id" => $emp->id,
                    "level" => $level,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
                array_push($data, $temp);

                $logData .= "\n" . $temp;
            }
            Employee_approver::insert($data);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "storeMulitple",
                "message",
                "Create new employee_approvers: " . $logData
            );
            //return "ok";
            // $asdf = app('ap')
            return app(EmployeeController::class)->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "storeMulitple",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

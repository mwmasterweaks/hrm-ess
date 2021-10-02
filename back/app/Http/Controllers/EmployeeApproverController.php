<?php

namespace App\Http\Controllers;

use App\Employee_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Approver;
use Carbon\Carbon;

class EmployeeApproverController extends Controller
{
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
            Employee_approver::create($request->except('level') + ["level" => $level]);
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
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
        try {
            /* $cmd  = Employee_approver::findOrFail($id);
            $input = $request->all();
            $cmd->fill($input)->save(); */
            DB::table('employee_approvers')
                ->where('approver_id', $request->approver_id)
                ->where('employee_id', $request->employee_id)
                ->update(['level' => $request->level]);

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Employee_approver::destroy($id);
            $value = explode(",", $id);
            $approver_id = $value[0];
            $employee_id = $value[1];
            DB::table('employee_approvers')
                ->where('approver_id', $approver_id)
                ->where('employee_id', $employee_id)
                ->delete();

            return $this->show($employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function storeMultiple(Request $request)
    {
        try {
            $employees = $request->employees;
            $approve_add = (object) $request->approve_add;
            $data = [];

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
            }
            Employee_approver::insert($data);
            //return "ok";
            // $asdf = app('ap')
            return app(EmployeeController::class)->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Employee_approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Approver;

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

            $cmd  = Employee_approver::findOrFail($id);

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
            Employee_approver::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

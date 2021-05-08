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
            $data = Employee_approver::create($request->except('level') + ["level" => $level]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Employee_Approver: " . $data
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
        try {

            $cmd  = Employee_approver::findOrFail($id);
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
                "update Employee_Approver id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = Employee_approver::findOrFail($id);
            Employee_approver::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Employee_Approver id " . $id .
                    "\nOld Employee_Approver: " . $tbl1
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

<?php

namespace App\Http\Controllers;

use App\department;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private $cname = "DepartmentController";
    public function index()
    {
        $tbl = department::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $department = department::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new department with ID: " . $department->id . "\nDetails: " .  $department
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
        $tbl = department::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(department $department)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = department::findOrFail($id);
            $logFrom = (clone $cmd);
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
                "Update department with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
        $value = explode(",", $id);
        $rowID = $value[0];
        $user_id = $value[1];
        $user_name = $value[2];

        try {
            $deleted = department::find($rowID);
            department::destroy($rowID);

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "message",
                "Delete department with ID: " . $rowID .
                    "\nDetails: " . $deleted
            );

            return $this->index();
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
}

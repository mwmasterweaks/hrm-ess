<?php

namespace App\Http\Controllers;

use App\leave_type;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    private $cname = "LeaveTypeController";
    public function index()
    {
        $tbl = leave_type::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $lt = leave_type::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new leave_type with ID: " . $lt->id . "\nDetails: " .  $lt
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
        $tbl = leave_type::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(leave_type $leave_type)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = leave_type::findOrFail($id);
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
                "Update leave_type with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $deleted = leave_type::find($rowID);
            leave_type::destroy($rowID);

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "message",
                "Delete leave_type with ID: " . $rowID .
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

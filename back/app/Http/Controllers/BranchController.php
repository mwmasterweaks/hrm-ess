<?php

namespace App\Http\Controllers;

use App\branch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    private $cname = "BranchController";
    public function index()
    {
        $tbl = branch::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $branch = branch::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new branch with ID: " . $branch->id . "\nDetails: " .  $branch
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
        $tbl = branch::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(branch $branch)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = branch::findOrFail($id);
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
                "Update branch with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $deleted = branch::find($rowID);
            branch::destroy($rowID);

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "message",
                "Delete branch with ID: " . $rowID .
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

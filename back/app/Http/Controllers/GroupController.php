<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Group;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    private $cname = "GroupController";
    public function index()
    {
        $tbl = Group::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $group = Group::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new group with ID: " . $group->id . "\nDetails: " .  $group
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
        $tbl = Group::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(Group $Group)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = Group::findOrFail($id);
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
                "Update group with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl = Employee::where('group_id', $rowID)->first();

            if (empty($tbl)) {
                $deleted = Group::find($rowID);
                Group::destroy($rowID);

                \Logger::instance()->log(
                    Carbon::now(),
                    $user_id,
                    $user_name,
                    $this->cname,
                    "destroy",
                    "message",
                    "Delete group with ID: " . $rowID .
                        "\nDetails: " . $deleted
                );

                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this Group, It\'s still in use.'], 500);
            }
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

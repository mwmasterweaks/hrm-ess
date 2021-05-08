<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
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
            $data = Group::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Group: " . $data
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
                "update Group id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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

            $tbl = Employee::where('group_id', $id)->first();

            if (empty($tbl)) {
                Group::destroy($id);

                \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Group id " . $id .
                        "\nOld Group: " . $tbl
                );
                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this Group, It\'s still in use.'], 500);
            }
        } catch (\Exception $ex) {
            \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Group id " . $id .
                        "\nOld Group: " . $tbl
                );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

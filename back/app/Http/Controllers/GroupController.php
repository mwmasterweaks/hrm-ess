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
            Group::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
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

            $tbl = Employee::where('group_id', $id)->first();

            if (empty($tbl)) {
                Group::destroy($id);
                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this Group, It\'s still in use.'], 500);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

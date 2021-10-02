<?php

namespace App\Http\Controllers;

use App\leave_type;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
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
            leave_type::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
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
            leave_type::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

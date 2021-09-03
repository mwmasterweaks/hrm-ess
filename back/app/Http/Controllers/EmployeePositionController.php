<?php

namespace App\Http\Controllers;

use App\employee_position;
use Illuminate\Http\Request;

class EmployeePositionController extends Controller
{
    public function index()
    {
        $tbl = employee_position::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            employee_position::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = employee_position::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(employee_position $employee_position)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = employee_position::findOrFail($id);

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
            employee_position::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

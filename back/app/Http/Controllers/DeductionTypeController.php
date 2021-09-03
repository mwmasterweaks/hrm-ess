<?php

namespace App\Http\Controllers;

use App\deduction_type;
use Illuminate\Http\Request;

class DeductionTypeController extends Controller
{
    public function index()
    {
        $tbl = deduction_type::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            deduction_type::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = deduction_type::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(deduction_type $deduction_type)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = deduction_type::findOrFail($id);

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
            deduction_type::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

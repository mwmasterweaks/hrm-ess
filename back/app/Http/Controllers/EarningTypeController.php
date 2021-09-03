<?php

namespace App\Http\Controllers;

use App\earning_type;
use Illuminate\Http\Request;

class EarningTypeController extends Controller
{
    public function index()
    {
        $tbl = earning_type::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            earning_type::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = earning_type::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(earning_type $earning_type)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = earning_type::findOrFail($id);

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
            earning_type::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

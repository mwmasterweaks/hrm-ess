<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        $tbl = Rate::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            Rate::create($request->except('name') + [
                'name' => str_replace( ',', '', number_format($request->daily_rate, 2))
            ]);
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = Rate::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(Rate $Rate)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = Rate::findOrFail($id);

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

            $tbl = Employee::where('rate_id', $id)->first();

            if (empty($tbl)) {
                Rate::destroy($id);
                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this rate, It\'s still in use.'], 500);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

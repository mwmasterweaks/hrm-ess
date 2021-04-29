<?php

namespace App\Http\Controllers;

use App\deduction;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    public function index()
    {
        $tbl = deduction::with(["type"])->orderBy("created_at", "DESC")->get();
        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            deduction::create($request->all());
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = deduction::with(["type"])
            ->where("employee_id", $id)
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json($tbl);
    }


    public function edit(deduction $deduction)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = deduction::findOrFail($id);

            $input = $request->all();

            $cmd->fill($input)->save();

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            deduction::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroyItem(Request $request)
    {
        try {
            deduction::destroy($request->id);

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

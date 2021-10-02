<?php

namespace App\Http\Controllers;

use App\branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
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
            branch::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
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
            branch::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\calendar_event;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    public function index()
    {
        $tbl = calendar_event::with(['branch'])->get();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            calendar_event::create($request->all());
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = calendar_event::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(calendar_event $calendar_event)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = calendar_event::findOrFail($id);

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
            calendar_event::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

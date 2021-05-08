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
            $data = Rate::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Rate: " . $data
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
                "update Rate id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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

            $tbl = Employee::where('rate_id', $id)->first();

            if (empty($tbl)) {
                Rate::destroy($id);

                \Logger::instance()->log(
                    Carbon::now(),
                    "",
                    "",
                    $this->cname,
                    "destroy",
                    "message",
                    "delete Rate id " . $id .
                        "\nOld Rate: " . $tbl
                );

                return $this->index();
            } else {
                return response()->json(['error' => 'Cant delete this rate, It\'s still in use.'], 500);
            }
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

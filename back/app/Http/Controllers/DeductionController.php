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
            $data = deduction::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Deduction: " . $data
            );

            return $this->show($request->employee_id);
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
                "update Deduction id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tbl1 = deduction::findOrFail($id);
            deduction::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Deduction id " . $id .
                    "\nOld Deduction: " . $tbl1
            );

            return $this->index();
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

    public function destroyItem(Request $request)
    {
        try {
            $tbl1 = deduction::findOrFail($request->id);
            deduction::destroy($request->id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Deduction id " . $request->id .
                    "\nOld Deduction: " . $tbl1
            );

            return $this->show($request->employee_id);
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

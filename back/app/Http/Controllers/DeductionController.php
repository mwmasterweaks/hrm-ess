<?php

namespace App\Http\Controllers;

use App\deduction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    private $cname = "DeductionController";
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
            $deduction = deduction::create($request->all());

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new deduction with ID: " . $deduction->id . "\nDetails: " .  $deduction
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
            $logFrom = (clone $cmd);
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
                "Update deduction with ID: " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
            );

            return $this->show($request->employee_id);
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
            deduction::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function destroyItem(Request $request)
    {
        try {
            $deleted = deduction::find($request->id);
            deduction::destroy($request->id);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroyItem",
                "message",
                "Delete deduction with ID: " . $request->id .
                    "\nDetails: " . $deleted
            );

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "destroyItem",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

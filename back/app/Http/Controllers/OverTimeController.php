<?php

namespace App\Http\Controllers;

use App\over_time;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OverTimeController extends Controller
{
    private $cname = "OverTimeController";
    public function index()
    {
        $tbl = over_time::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $fileName = "noattachment.png";
            if ($request->attachment != "") {

                $exploded = explode(',', $request->attachment);

                $decoded = base64_decode($exploded[1]);

                if (str_contains($exploded[0], "jpeg"))
                    $extension = "jpeg";
                elseif (str_contains($exploded[0], "jpg"))
                    $extension = "jpg";
                elseif (str_contains($exploded[0], "png"))
                    $extension = "png";
                elseif (str_contains($exploded[0], "gif"))
                    $extension = "gif";
                elseif (str_contains($exploded[0], "tiff"))
                    $extension = "tiff";
                elseif (str_contains($exploded[0], "bmp"))
                    $extension = "bmp";
                else
                    $extension = "txt";

                $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                $path = public_path() . "/attachments/" . $fileName;

                file_put_contents($path, $decoded);
            }
            $tblInserted = over_time::create($request->except('attachment') + [
                "attachment" => $fileName,
                "date_filed" => new Carbon(),
                "approve_level" => "1"
            ]);
            over_time::where("id", $tblInserted->id)
                ->update(['reference_no' => 'OT-' . $tblInserted->id]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->employee_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new overtime with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted
            );

            DB::commit();
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            DB::rollBack();
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
        $tbl = over_time::where("employee_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(over_time $over_time)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = over_time::findOrFail($id);

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
            over_time::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function cancelApp(Request $request)
    {
        try {
            DB::table('over_times')
                ->where('reference_no', $request->reference_no)
                ->update(['status' => 'Canceled']);

            $ot = over_time::where('reference_no', $request->reference_no);
            $ots = tap($ot->first(), function($row) {
                $row->status = 'Canceled';
                $row->save();
            });

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "cancelApp",
                "Update status to 'Canceled'",
                "Cancel overtime with ID: " . $request->id .
                    "\nDetails: " . $ots
            );
            return $this->show($request->user_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "cancelApp",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

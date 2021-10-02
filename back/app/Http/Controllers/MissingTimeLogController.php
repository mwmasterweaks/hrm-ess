<?php

namespace App\Http\Controllers;

use App\missing_time_log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MissingTimeLogController extends Controller
{
    public function index()
    {
        $tbl = missing_time_log::all();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

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
            $tblInserted = missing_time_log::create($request->except('attachment') + [
                "attachment" => $fileName,
                "date_filed" => new Carbon(),
                "approve_level" => "1"
            ]);
            missing_time_log::where("id", $tblInserted->id)
                ->update(['reference_no' => 'MTL-' . $tblInserted->id]);
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = missing_time_log::where("employee_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(missing_time_log $missing_time_log)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = missing_time_log::findOrFail($id);

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
            missing_time_log::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function cancelApp(Request $request)
    {
        try {

            DB::table('missing_time_logs')
                ->where('reference_no', $request->reference_no)
                ->update(['status' => 'Canceled']);

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

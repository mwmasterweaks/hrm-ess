<?php

namespace App\Http\Controllers;

use App\missing_time_log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MissingTimeLogController extends Controller
{
    private $cname = "MissingTimeLogController";
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

            $date_filed = new Carbon();

            $tblInserted = missing_time_log::create($request->except('attachment') + [
                "attachment" => $fileName,
                "date_filed" => $date_filed,
                "approve_level" => "1"
            ]);
            missing_time_log::where("id", $tblInserted->id)
                ->update(['reference_no' => 'MTL-' . $tblInserted->id]);

            if (true) {
                $message = "
                <html>
                    <head>
                    </head>
                    <body>
                        " . $request->msg . "
                    </body>
                    <style>
                        .my-td {
                            padding: 10px;
                            padding-left: 20px;
                            padding-right: 20px;
                        }
                        .my-table {
                            border-radius: 10px 10px 0 0;
                            border-bottom: 5px solid #547e6a;
                        }
                        .my-table,
                        .my-table > tr {
                            background: #e7fff4;
                            font-family: 'Helvetica';
                        }
                        .head-bg {
                            color: #ffffff;
                            background: #098b4f;
                            border-radius: 10px 10px 0 0;
                            letter-spacing: 0.1em;
                            font-weight: bold;
                            padding: 20px;
                            padding-left: 40px;
                            padding-right: 40px;
                            text-align: center;
                        }
                        .my-table > tr:nth-child(even) {
                            background: #f1fff9;
                        }
                        .name-bg {
                            color: #ffffff;
                            background: #3d3d3d;
                            /* font-weight: bold; */
                            text-align: left;
                        }
                        .my-table {
                            border-collapse: collapse;
                        }
                    </style>
                </html>";
                $message = str_replace("REFNUM", "MTL-" . $tblInserted->id, $message);
                $message = str_replace("DATEFILED", $date_filed, $message);
            }

            \Logger::instance()->mailerZimbra(
                "HRMESS - REQUEST FOR MISSING TIME LOGS",
                $message,
                $request->user_email,
                $request->user_name,
                $request->sendTo,
                $request->CCto
            );

            \Logger::instance()->log(
                Carbon::now(),
                $request->employee_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new missing_time_log with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted
            );

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->employee_id,
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

            $mtl = missing_time_log::where('reference_no', $request->reference_no);
            $mtls = tap($mtl->first(), function($row) {
                $row->status = 'Canceled';
                $row->save();
            });

            \Logger::instance()->log(
                Carbon::now(),
                $request->employee_id,
                $request->user_name,
                $this->cname,
                "cancelApp",
                "Update status to 'Canceled'",
                "Cancel missing_time_log with ID: " . $request->id .
                    "\nDetails: " . $mtls
            );

            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->employee_id,
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

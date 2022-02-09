<?php

namespace App\Http\Controllers;

use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\leave_balance;
use App\leave_day;
use App\leave_type;
use LeaveDay;
use stdClass;

class LeaveController extends Controller
{
    private $cname = "LeaveController";
    public function index()
    {
        $tbl = Leave::all();

        return response()->json($tbl);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        return $request;
        try {
            DB::beginTransaction();

            // $date_from = new Carbon($request->date_from);
            // $date_to = new Carbon($request->date_to);
            // $daysCount = $date_from->diffInDays($date_to);
            // $daysCount += 1;

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

            $tblInserted = Leave::create($request->except('attachment', 'total_days', 'date_filed', 'approve_level') + [
                "attachment" => $fileName,
                "total_days" => $request->total_days,
                "date_filed" => $date_filed,
                "approve_level" => "1"
            ]);

            $leave_type = leave_type::where('id', $tblInserted->leave_type_id)->value('name');

            $log = tap(Leave::where("id", $tblInserted->id))
                ->update(['reference_no' => 'LV-' . $tblInserted->id])
                ->first();

            $lds = "";

            foreach ($request->daysList as $item) {
                $item = (object) $item;
                if ($item->is_rest_day == 0) {
                    $halfday = (int) $item->halfday;
                    $halfday_type = (int) $item->halfday_type;
                    if ($halfday == 1 && $halfday_type == 0) $halfday_type = 1;
                    $ld = leave_day::create(
                        [
                            'leave_id' => $tblInserted->id,
                            'leave_date' => $item->work_date,
                            'halfday' => $halfday,
                            'halfday_type' => $halfday_type
                        ]
                    );
                    $lds .= "\n" . $ld;
                }
            }

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
                $message = str_replace("REFNUM", "LV-" . $tblInserted->id, $message);
                $message = str_replace("LEAVETYPE", $leave_type, $message);
                $message = str_replace("DATEFILED", $date_filed, $message);
                $message = str_replace("TOTALDAYS", $request->total_days, $message);
            }

            \Logger::instance()->mailerZimbra(
                "HRMESS - REQUEST FOR LEAVE OF ABSENCE",
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
                "Create new leave with ID: " . $tblInserted->id . "\nDetails: " .  $log .
                "\nCreate new leave day/s: " . $lds
            );

            DB::commit();
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            DB::rollBack();
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
        $tbl = Leave::with(['leave_type', 'leave_days'])->where("employee_id", $id)->get();

        return response()->json($tbl);
    }

    public function edit(Leave $Leave)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {

            $cmd  = Leave::findOrFail($id);

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
            Leave::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function cancelApp(Request $request)
    {
        try {

            $leave = Leave::where('reference_no', $request->reference_no);
            $leaves = tap($leave->first(), function($row) {
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
                "Cancel leave with ID: " . $request->id .
                    "\nDetails: " . $leaves
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

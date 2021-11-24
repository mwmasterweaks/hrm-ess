<?php

namespace App\Http\Controllers;

use App\manual_attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManualAttendanceController extends Controller
{
    private $cname = "ManualAttendanceController";
    public function index()
    {
        $tbl = manual_attendance::all();

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
            $data = "";
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

            if ($request->multiple_apply == true) {

                foreach ($request->daysList as $item) {
                    $item = (object) $item;
                    if ($item->is_rest_day == 0) {
                        $tblInserted = manual_attendance::create(
                            [
                                'employee_id' => $item->employee_id,
                                'work_date' => $item->work_date,
                                'shift' => 'From: ' . $item->shift_sched_in . 'To: ' . $item->shift_sched_out,
                                'time_in' => $item->time_in,
                                'time_out' => $item->time_out,
                                'reference_no' => 'tempnumber123',
                                'reason' => $request->reason,
                                'attachment' => $fileName,
                                'date_filed' => new Carbon(),
                                'approve_level' => "1"
                            ]
                        );

                        manual_attendance::where("id", $tblInserted)
                            ->update(['reference_no' => 'MA-' . $tblInserted]);

                        $data .= "\nCreate new manual_attendance with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted;
                    }
                }
            } else {
                $tblInserted = manual_attendance::create($request->except('attachment') + [
                    "attachment" => $fileName,
                    "date_filed" => new Carbon(),
                    "approve_level" => "1"
                ]);
                manual_attendance::where("id", $tblInserted->id)
                    ->update(['reference_no' => 'MA-' . $tblInserted->id]);

                $data = "Create new manual_attendance with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted;
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->employee_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                $data
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
        $tbl = manual_attendance::where("employee_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(manual_attendance $manual_attendance)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = manual_attendance::findOrFail($id);

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
            manual_attendance::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function cancelApp(Request $request)
    {
        try {

            $ma = manual_attendance::where('reference_no', $request->reference_no);
            $mas = tap($ma->first(), function($row) {
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
                "Cancel manual_attendance with ID: " . $request->id .
                    "\nDetails: " . $mas
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

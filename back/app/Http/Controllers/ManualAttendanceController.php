<?php

namespace App\Http\Controllers;

use App\manual_attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ManualAttendanceController extends Controller
{
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
                        $tblInserted = DB::table('manual_attendances')->insertGetId(
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
                                'approve_level' => "1",
                                'created_at' => new Carbon(),
                                'updated_at' => new Carbon()
                            ]
                        );

                        manual_attendance::where("id", $tblInserted)
                            ->update(['reference_no' => 'MA-' . $tblInserted]);

                        $cmd = manual_attendance::find($tblInserted);
                        $data = $cmd->replicate();

                        \Logger::instance()->log(
                            Carbon::now(),
                            $request->user_id,
                            $request->user_name,
                            $this->cname,
                            "store",
                            "message",
                            "Create new Manual_Attendance: " . $data
                        );

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

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    "Create new Manual_Attendance: " . $tblInserted
                );
            }


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
                "update Manual_Attendance id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = manual_attendance::findOrFail($id);
            manual_attendance::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Manual_Attendance id " . $id .
                    "\nOld Manual_Attendance: " . $tbl1
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

    public function cancelApp(Request $request)
    {
        try {

            DB::table('manual_attendances')
                ->where('reference_no', $request->reference_no)
                ->update(['status' => 'Canceled']);

            return $this->show($request->user_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

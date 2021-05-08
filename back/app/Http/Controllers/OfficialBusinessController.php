<?php

namespace App\Http\Controllers;

use App\official_business;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OfficialBusinessController extends Controller
{
    public function index()
    {
        $tbl = official_business::all();

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
                        $tblInserted = DB::table('official_businesses')->insertGetId(
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

                        official_business::where("id", $tblInserted)
                            ->update(['reference_no' => 'OB-' . $tblInserted]);

                        $cmd = official_business::find($tblInserted);
                        $data = $cmd->replicate();

                        \Logger::instance()->log(
                            Carbon::now(),
                            $request->user_id,
                            $request->user_name,
                            $this->cname,
                            "store",
                            "message",
                            "Create new Official_Business: " . $data
                        );
                    }
                }
            } else {
                $tblInserted = official_business::create($request->except('attachment') + [
                    "attachment" => $fileName,
                    "date_filed" => new Carbon(),
                    "approve_level" => "1"
                ]);
                official_business::where("id", $tblInserted->id)
                    ->update(['reference_no' => 'OB-' . $tblInserted->id]);

                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user_id,
                    $request->user_name,
                    $this->cname,
                    "store",
                    "message",
                    "Create new Official_Business: " . $tblInserted
                );
            }
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $tbl = official_business::where("employee_id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(official_business $official_business)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = official_business::findOrFail($id);
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
                "update Official_Business id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = official_business::findOrFail($id);
            official_business::destroy($id);

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

            DB::table('official_businesses')
                ->where('reference_no', $request->reference_no)
                ->update(['status' => 'Canceled']);

            return $this->show($request->user_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

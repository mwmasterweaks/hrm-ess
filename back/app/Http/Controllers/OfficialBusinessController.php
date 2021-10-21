<?php

namespace App\Http\Controllers;

use App\official_business;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OfficialBusinessController extends Controller
{
    private $cname = "OfficialBusinessController";
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
                $data = "";
                foreach ($request->daysList as $item) {
                    $item = (object) $item;
                    if ($item->is_rest_day == 0) {
                        /* $tblInserted = DB::table('official_businesses')->insertGetId(
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
                        ); */

                        $tblInserted = official_business::create(
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

                        official_business::where("id", $tblInserted->id)
                            ->update(['reference_no' => 'OB-' . $tblInserted->id]);

                        $data .= "\nCreate new official business with ID: " .
                            $tblInserted->id . "\nDetails: " .  $tblInserted;
                    }
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
                    "Create new official business with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted
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
            official_business::destroy($id);

            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function cancelApp(Request $request)
    {
        try {

            $ob = official_business::where('reference_no', $request->reference_no);
            $obs = tap($ob->first(), function($row) {
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
                "Cancel official business with ID: " . $request->id .
                    "\nDetails: " . $obs
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

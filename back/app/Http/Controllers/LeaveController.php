<?php

namespace App\Http\Controllers;

use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\leave_balance;
use App\leave_day;
use LeaveDay;

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
        // return $request;
        try {

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

            $tblInserted = Leave::create($request->except('attachment', 'total_days', 'date_filed', 'approve_level') + [
                "attachment" => $fileName,
                "total_days" => $request->total_days,
                "date_filed" => new Carbon(),
                "approve_level" => "1"
            ]);
            //update leave balance
            Leave::where("id", $tblInserted->id)
                ->update(['reference_no' => 'LV-' . $tblInserted->id]);

            $lds = "";
            foreach ($request->daysList as $item) {
                $item = (object) $item;
                if ($item->is_rest_day == 0) {
                    /* $ld = DB::table('leave_days')->insert(
                        [
                            'leave_id' => $tblInserted->id,
                            'leave_date' => $item->work_date,
                            'halfday' => $item->haftday,
                            'halfday_type' => $item->haftday_type,
                            'created_at' => new Carbon(),
                            'updated_at' => new Carbon()
                        ]
                    ); */
                    $ld = tap(new leave_day, function($row) use($tblInserted, $item) {
                        $row->leave_id = $tblInserted->id;
                        $row->leave_date = $item->work_date;
                        $row->halfday = $item->haftday;
                        $row->halfday_type = $item->haftday_type;
                        $row->created_at = new Carbon();
                        $row->updated_at = new Carbon();
                    });
                    $lds .= "\n" . $ld;
                }
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new leave with ID: " . $tblInserted->id . "\nDetails: " .  $tblInserted .
                "\nCreate new leave day/s: " . $lds
            );

            //DEDUCT BALANCE NEED TO MOVE IN APPROVE
            // $tbl = DB::table('leave_balances')
            //     ->where("employee_id", $request->employee_id)
            //     ->where("leave_type_id", $request->leave_type_id)
            //     ->get();
            // $temp = $daysCount;
            // foreach ($tbl as $item) {
            //     if ($temp > $item->balance) {
            //         leave_balance::where("id", $item->id)
            //             ->update(['balance' => '0']);
            //         leave_balance::where("id", $item->id)
            //             ->increment('availed', $item->balance);
            //         $temp = $temp - $item->balance;
            //     } else {
            //         leave_balance::where("id", $item->id)
            //             ->decrement('balance', $temp);
            //         leave_balance::where("id", $item->id)
            //             ->increment('availed', $temp);
            //     }
            // }
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
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

            DB::table('leaves')
                ->where('reference_no', $request->reference_no)
                ->update(['status' => 'Canceled']);

            return $this->show($request->user_id);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}

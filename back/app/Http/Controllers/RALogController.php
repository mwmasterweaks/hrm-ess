<?php

namespace App\Http\Controllers;

use App\RALog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RALogController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(RALog $rALog)
    {
        //
    }

    public function edit(RALog $rALog)
    {
        //
    }

    public function update(Request $request, RALog $rALog)
    {
        //
    }

    public function destroy(RALog $rALog)
    {
        //
    }

    public function updateLog()
    {
        try {
            $RALog = RALog::max('id');
            //select max(id) from r_a_logs
            //430000

            $tbl = DB::connection('sqlsrv')->select("select [RN] as rn, [UID] as employeeID, [Date] as datetime
                from [FASSQL].[dbo].[RALog] where [RN] > " . $RALog);

            // $tbl = DB::connection('sqlsrv')->select("select [RN] as rn, [UID] as employeeID, [Date] as datetime
            //    from [FASSQL].[dbo].[RALog] where [RN] > 410000 and [RN] < 430000");
            $temp = [];
            foreach ($tbl as $i => $d) {
                $temp[$i] = [
                    'id' => $d->rn,
                    'employeeID' => $d->employeeID,
                    'datetime' => $d->datetime
                ];
            }
            DB::table('r_a_logs')->insert($temp);

            return "ok";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }
}

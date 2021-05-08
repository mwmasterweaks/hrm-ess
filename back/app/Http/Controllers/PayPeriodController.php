<?php

namespace App\Http\Controllers;

use App\Pay_period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class PayPeriodController extends Controller
{
    public function index()
    {
        $tbl = Pay_period::with(['group'])->orderBy('period', 'DESC')->get();

        return response()->json($tbl);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            //return $request->data;
            $data = array();

            //return count($request->data);
            foreach ($request->data as $item) {

                $item = (object) $item;
                //array_push($data, $item->period);
                array_push($data, array(
                    'period' => $item->period,
                    'frequency' => $item->frequency,
                    'group_id' => $item->group_id,
                    'year' => $item->year,
                    'from' => $item->from,
                    'to' => $item->to,
                ));
            }
            //return $data;
            Pay_period::insert($data);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new Pay_Period: " . $data
            );
            return $this->index();
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
        $tbl = Pay_period::where("id", $id)->get();

        return response()->json($tbl);
    }


    public function edit(Pay_period $Pay_period)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {

            $cmd  = Pay_period::findOrFail($id);
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
                "update Pay_Period id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            $tbl1 = Pay_period::findOrFail($id);
            Pay_period::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Pay_Period id " . $id .
                    "\nOld Pay_Period: " . $tbl1
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

    public function generatePayPeriod(Request $request)
    {

        $freq = $request->frequency;
        $year = $request->year;
        $g_id = $request->group_id;
        $period = $request->pay_period_selected;
        $date = Carbon::create($year, 1, 1, 0, 0, 0);
        $days = 0;

        $temp = [];
        if ($period == 2) {
            $days = $request->days;
        } else {
        }

        if ($freq == "weekly") {
            do {

                $c = collect();
                $c->put('from', (new Carbon($date))->toDateString());
                while ($date->englishDayOfWeek != "Sunday")
                    $date = $date->addDay();

                $c->put('to', (new Carbon($date))->toDateString());
                $dtperiod = new Carbon($date);
                if ($period == 2)
                    $dtperiod = $dtperiod->addDays($days);

                $c->put('period', $dtperiod->toDateString());
                $c->put('frequency', 'Weekly');
                $c->put('group_id', $g_id);
                $c->put('year', $year);
                //murag dapat dili si c ang e add push dapat object
                $newCollection = new Collection($c);
                array_push($temp, $newCollection);

                $date = $date->addDay();
            } while ($year == $date->year);
        }
        if ($freq == "semi") {

            $date = $date->subDays(4);
            while ($year >= $date->year) {
                $c = collect();
                $c->put('from', (new Carbon($date))->toDateString());
                //$date = $date->addDays(14);
                if ($date->day == 28) {
                    $date = $date->addMonth();
                    $date->day = 12;
                    $dtperiod = new Carbon($date);
                    $dtperiod->day = 15;
                    if ($year < $date->year)
                        break;
                } else {
                    $date->day = 27;
                    $dtperiod = new Carbon($date);
                    $dtperiod->day = $dtperiod->daysInMonth;
                }


                $c->put('to', (new Carbon($date))->toDateString());

                if ($period == 2)
                    $dtperiod = $dtperiod->addDays($days);

                $c->put('period', $dtperiod->toDateString());
                $c->put('frequency', 'Semi-monthly');
                $c->put('group_id', $g_id);
                $c->put('year', $year);
                //murag dapat dili si c ang e add push dapat object
                $newCollection = new Collection($c);
                array_push($temp, $newCollection);

                $date = $date->addDay();
            };
        }
        if ($freq == "monthly") {
            do {
                $c = collect();
                $c->put('from', (new Carbon($date))->toDateString());
                if ($date->day == 1)
                    $date->day =  $date->daysInMonth;

                $c->put('to', (new Carbon($date))->toDateString());
                $dtperiod = new Carbon($date);
                if ($period == 2)
                    $dtperiod = $dtperiod->addDays($days);

                $c->put('period', $dtperiod->toDateString());
                $c->put('frequency', 'Monthly');
                $c->put('group_id', $g_id);
                $c->put('year', $year);
                //murag dapat dili si c ang e add push dapat object
                $newCollection = new Collection($c);
                array_push($temp, $newCollection);

                $date = $date->addDay();
            } while ($year == $date->year);
        }
        return $temp;
    }

    public function getYear()
    {
        $tbl = DB::table('pay_periods')
            ->select("year")
            ->groupBy("year")
            ->get();

        return response()->json($tbl);
    }

    public function getMonth($year)
    {
        $tbl = Pay_period::where("period", "like", $year . "%")->get();

        $months = [];
        foreach ($tbl as $item) {
            $date = new Carbon($item->period);
            $c = collect();
            $c->put('month', $date->month);
            if (!in_array($c, $months)) {
                array_push($months, $c);
            }
        }

        return response()->json($months);
    }
    public function getDay($yearMonth)
    {
        $tbl = Pay_period::where("period", "like", $yearMonth . "%")->get();
        $day = [];
        foreach ($tbl as $item) {
            $date = new Carbon($item->period);
            $item->day =  $date->day;
            // $c = collect();
            // $c->put('day', $date->day);
            // $c->put('id', $item->id);
            // $c->put('from', $item->from);
            // $c->put('to', $item->to);
            array_push($day, $item);
        }
        return response()->json($day);
    }
}

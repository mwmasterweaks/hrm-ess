<?php

namespace App\Http\Controllers;

use App\payslip;
use App\Rate;
use App\over_time;
use App\Leave;
use App\earning;
use App\deduction;
use App\dtr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use stdClass;

class PayslipController extends Controller
{
    public function index()
    {
        $tbl = payslip::all();
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
            DB::beginTransaction();
            $payroll = payslip::where('employee_id', $request->employee_id)
                ->where('pay_period_id', $request->pay_period_id)->first();

            if ($payroll == null) {
                payslip::create($request->all());
            } else {
                $cmd  = payslip::findOrFail($payroll->id);
                $cmd->fill($request->all())->save();
            }

            if ($request->ot != null) {
                foreach ($request->ot as $item) {
                    $item = (object) $item;
                    over_time::where('id', $item->id)->update(['status_paid' => 'yes']);
                }
            }
            DB::commit();
            return $this->show($request->employee_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex], 500);
        }
    }
    public function show($id)
    {
        $tbl = payslip::with(['pay_period','employee.branch', 'employee.department', 'employee.position'])->where("employee_id", $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tbl);
    }
    public function edit(payslip $payslip)
    {
        //
    }
    public function update(Request $request, payslip $payslip)
    {
        //
    }
    public function destroy(payslip $payslip)
    {
        //
    }
    public function generatePayslip(Request $request)
    {
        //------------------------------IMPORTANT DAPAT MA DEDUCT SA SWELDO ANG LEAVE WITHOUT PAY ---------------------------
        $emp = (object) $request->employee;
        // return json_encode($emp);
        $payperiod = (object) $request->payperiod;
        $c1 = collect();
        $rate = Rate::where('id', $emp->rate_id)->first();
        $basicPay = round($rate->daily_rate * 13);
        $grossPay = $basicPay;
        $totalDeduct = 0;

        //get OT
        $ot = over_time::where('employee_id', $emp->id)
            ->where('status', "Approved")
            ->where('approve_date', '>=', $payperiod->from)
            ->where('approve_date', '<=', $payperiod->to)
            ->where('status_paid', "no")
            ->get();

        $totalOTHours = 0;
        $totalROTHours = 0;
        $totalRHOTHours = 0;
        $totalSHOTHours = 0;
        $totalSHRDOTHours = 0;
        //Regular OT for meantime
        $regularOTRate = ($rate->daily_rate * 1.25) / 8;
        $LHoliOTRate = ($rate->daily_rate * 2) / 8;
        $SHoliOTRate = ($rate->daily_rate * 1.3) / 8;
        $SHoliRDOTRate = ($rate->daily_rate * 1.5) / 8;
        foreach ($ot as $item) {
            $totalOTHours += $item->total_hours;
            if ($item->type == "Regular Overtime") $totalROTHours += $item->total_hours;
            if ($item->type == "Regular Holiday Overtime") $totalRHOTHours += $item->total_hours;
            if ($item->type == "Special Holiday Overtime") $totalSHOTHours += $item->total_hours;
            if ($item->type == "Special Holiday on Rest Day Overtime") $totalSHRDOTHours += $item->total_hours;
        }

        $rot_pay = $totalROTHours * $regularOTRate;
        $rhot_pay = $totalRHOTHours * $LHoliOTRate;
        $shot_pay = $totalSHOTHours * $SHoliOTRate;
        $shrdot_pay = $totalSHRDOTHours * $SHoliRDOTRate;
        $ot_pay = $rot_pay + $rhot_pay + $shot_pay + $shrdot_pay;
        // $ot_pay = $totalOTHours * $regularOTRate;
        $grossPay += round($ot_pay, 2);
        // $grossPay -= round($leave_wo_pay, 2);

        //get earnings
        $earns = earning::with('type')->where('employee_id', $emp->id)
            ->where('effective_date', "<=", $payperiod->period)
            ->where('end_date', ">=", $payperiod->period)
            ->orWhereNull('end_date')
            ->where('effective_date', "<=", $payperiod->period)
            ->get();
        foreach ($earns as $item) {
            $type = (object) $item->type;
            if ($type->id == '4')
                $c1->put('thirteen_month_pay', $item->amount);
            else
                $c1->put(strtolower(str_replace(' ', '_', $type->name)), $item->amount);
            $grossPay += $item->amount;
        }
        //TOTAL GROSS^^^^^


        //get absent, late. undertime
        $dtr_deduction = $this->getLateAbsent($emp->id, $payperiod->id, $rate->daily_rate, $emp->branch_id, $payperiod->from, $payperiod->to);
        //$dtr_deduction = (object) $dtr_deduction;
        $grossPay += $dtr_deduction['night_add'];
        $totalDeduct += $dtr_deduction['late_deduction'];
        $totalDeduct += $dtr_deduction['absent_deduction'];
        $totalDeduct += $rate->sss_deduction;
        $totalDeduct += $rate->phic_deduction;
        $totalDeduct += $rate->hdmf_deduction;
        //get deduction
        $deduct = deduction::with('type')->where('employee_id', $emp->id)
            ->where('effective_date', "<=", $payperiod->period)
            ->where('end_date', ">=", $payperiod->period)
            ->orWhereNull('end_date')
            ->where('effective_date', "<=", $payperiod->period)
            ->where('employee_id', $emp->id)
            ->get();
        foreach ($deduct as $item) {
            $type = (object) $item->type;
            $c1->put(strtolower(str_replace(' ', '_', $type->name)), $item->amount);
            $totalDeduct += $item->amount;
        }
        $totalDeduct = round($totalDeduct, 2);
        $grossPay = round($grossPay, 2);
        $net_pay = $grossPay - $totalDeduct;
        $c1->put('basic_pay', $basicPay);
        $c1->put('ot_num_hours', $totalOTHours);
        $c1->put('ot_pay', round($ot_pay, 2));
        $c1->put('ot', $ot);
        $c1->put('earns', $earns);
        $c1->put('deduct', $deduct);

        //$c1->put('dtr', $dtr_deduction);
        $c1->put('night_differential', $dtr_deduction['night_add']);
        $c1->put('num_of_days_absent', $dtr_deduction['absent']);
        $c1->put('absent_deduction', $dtr_deduction['absent_deduction']);
        $c1->put('num_of_minute_late', $dtr_deduction['num_of_minute_late']);
        $c1->put('late_deduction', $dtr_deduction['late_deduction']);


        $c1->put('sss', $rate->sss_deduction);
        $c1->put('phic', $rate->phic_deduction);
        $c1->put('hdmf', $rate->hdmf_deduction);
        $c1->put('total_gross_pay', $grossPay);
        $c1->put('total_deduction', $totalDeduct);
        $c1->put('net_pay', round($net_pay, 2));

        return $c1;
    }
    public function getLateAbsent($emp_id, $period_id, $dailyrate, $branch_id, $period_from, $period_to)
    {
        $tbl = dtr::where("employee_id", $emp_id)->where("pay_period_id", $period_id)->get();

        $c1 = collect();
        $absent = 0;
        $late = 0;
        $undertime = 0;
        $temp = 0;
        $total_hour_night = 0;
        $rd = 0;
        $ld_count = 0;
        if (count($tbl) > 0)
            foreach ($tbl as $dtr) {

                //holiday
                $checkIfHoliday = false;
                $calendar = [];
                if ($dtr->work_date != null) {
                    $calendar = DB::table('calendar_events')
                        ->where('type', 'holiday')
                        ->where('frequency', 'This year only')
                        ->where('calendar_events.from', '<=', $dtr->work_date)
                        ->where('calendar_events.to', '>=', $dtr->work_date)
                        ->where('branch_id', '0')
                        ->orWhere('branch_id', $branch_id)
                        ->where('type', 'holiday')
                        ->where('frequency', 'This year only')
                        ->where('calendar_events.from', '<=', $dtr->work_date)
                        ->where('calendar_events.to', '>=', $dtr->work_date)
                        ->get();
                    if (count($calendar) > 0)
                        $checkIfHoliday = true;
                }

                if ($checkIfHoliday != true) {
                    $dtrCbn = new Carbon($dtr->work_date);
                    $dtrYear =  $dtrCbn->year;
                    if ($dtr->work_date != null)
                        // SELECT * FROM `calendar_events`
                        // WHERE type = 'holiday' and frequency = 'yearly'
                        // and  MONTH(calendar_events.from) <= MONTH('2020-06-12') and  MONTH(calendar_events.to) >= MONTH('2020-06-12')
                        // and  DAY(calendar_events.from) <= DAY('2020-06-12') and  DAY(calendar_events.to) >= DAY('2020-06-12')
                        $calendar1 = DB::table('calendar_events')
                            ->where('type', 'holiday')
                            ->where('frequency', 'Yearly')
                            ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                            ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                            ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                            ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                            ->where('branch_id', '0')
                            ->orWhere('branch_id', $branch_id)
                            ->where('type', 'holiday')
                            ->where('frequency', 'Yearly')
                            ->where(DB::raw('MONTH(calendar_events.from)'), '<=', $dtrCbn->month)
                            ->where(DB::raw('MONTH(calendar_events.to)'), '>=',  $dtrCbn->month)
                            ->where(DB::raw('DAY(calendar_events.from)'), '<=', $dtrCbn->day)
                            ->where(DB::raw('DAY(calendar_events.to)'), '>=',  $dtrCbn->day)
                            ->get();
                    if (count($calendar1) > 0)
                        $checkIfHoliday = true;
                }
                //holiday
                if (!$checkIfHoliday)
                    if (!$dtr->is_rest_day)
                        if ($dtr->time_in != null || $dtr->time_out != null) {
                            if ($dtr->time_in != null && $dtr->time_out != null) {

                                $base_in = new Carbon($dtr->shift_sched_in);
                                $user_in = new Carbon($dtr->time_in);
                                $base_out = new Carbon($dtr->shift_sched_out);
                                $user_out = new Carbon($dtr->time_out);
                                $night_hour_start = new Carbon($dtr->shift_sched_in);
                                $night_hour_end = new Carbon($dtr->shift_sched_in);
                                $night_hour_start->hour = 19;
                                $night_hour_start->minute = 0;
                                $night_hour_end = $night_hour_end->addDay();
                                $night_hour_end->hour = 7;
                                $night_hour_end->minute = 0;

                                if ($base_in < $user_in)
                                    $late += $base_in->diffInMinutes($user_in);
                                if ($base_out > $user_out)
                                    $undertime += $user_out->diffInMinutes($base_out);

                                // if ($base_in >= $night_hour_start) {
                                //     $temp = new Carbon($dtr->time_in);
                                //     while ($temp > $night_hour_end) {
                                //         $temp->addHour();
                                //         $total_hour_night++;
                                //     }
                                // }
                                if ($base_out > $night_hour_start) {
                                    $temp = new Carbon($dtr->time_in);
                                    $total_night_diff_per_day = 0;
                                    while ($temp < $night_hour_end) {
                                        if ($total_night_diff_per_day == 6)
                                            break;
                                        if ($base_out < $temp)
                                            break;
                                        if ($temp > $night_hour_start) {
                                            $total_hour_night++;
                                            $total_night_diff_per_day++;
                                        }
                                        $temp->addHour();
                                    }
                                }
                            } else
                                $temp++;
                        } else
                            $absent++;
                    else
                        $rd++;
                else {
                    unset($dtr->is_rest_day);
                    $dtr->is_rest_day = 3;
                }
            }

        //get allowance
        $allowance = 0;
        $allowance_query = earning::where('employee_id', $emp_id)
            ->where('effective_date', '>=', $period_from)
            ->where('effective_date', '<=', $period_to)
            ->where('earning_type_id', 6)
            ->orWhere('employee_id', $emp_id)
            ->where('end_date', '>=', $period_from)
            ->where('end_date', '<=', $period_to)
            ->where('earning_type_id', 6)
            ->orWhere('employee_id', $emp_id)
            ->where('effective_date', '<=', $period_from)
            ->where('end_date', '>=', $period_to)
            ->where('earning_type_id', 6);
        $allowances = (clone $allowance_query)->get();
        $earning_allowance = (clone $allowance_query)->first();
        if (count($allowances) > 0)
            $allowance = $earning_allowance->amount;

        //get leave
        $leaves = Leave::where('employee_id', $emp_id)
            ->where('date_from', '>=', $period_from)
            ->where('date_from', '<=', $period_to)
            ->where('status', "Approved")
            ->where('leave_type_id', 1)
            ->get();

        foreach ($leaves as $item) {
            $leave_days = DB::table('leave_days')
                ->where('leave_id', $item->id)
                ->where('leave_date', '<=', $period_to)
                ->get();

            foreach ($leave_days as $item) {
                if ($item->halfday == 0)
                    $ld_count++;
                else $ld_count += 0.5;
            }
        }

        $absent += $ld_count;
        // $totallate = $late * 0.88; not needed

        $late_deduct = ($dailyrate / 8 / 60) * $late + (($allowance / 13) / 8 / 60) * $late;
        $undertime_deduct = ($dailyrate / 8 / 60) * $undertime;
        $absent_deduct = $dailyrate * $absent;
        $night_add = ($dailyrate / 8 * 0.1) * $total_hour_night;
        $c1->put('night_add',  round($night_add, 2));
        $c1->put('total_hour_night',  $total_hour_night);
        $c1->put('late_deduction',  round($late_deduct, 2));
        $c1->put('undertime_deduct',  round($undertime_deduct, 2));
        $c1->put('absent_deduction',  round($absent_deduct, 2));
        $c1->put('num_of_minute_late',  $late);
        $c1->put('undertime',  $undertime);
        $c1->put('absent',  $absent);

        return $c1;
    }
}

<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Approver;
use App\Rate;
use App\employee_status_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;
use Carbon\CarbonPeriod;
use stdClass;

class EmployeeController extends Controller
{
    private $cname = "EmployeeController";
    public function index()
    {
        try {
            $tbl = Employee::with($this->empWith())
            ->where('employment_status', 'Trainee')
            ->whereHas('employeeStatus', function($query) {
                $query
                    ->where('status', 'Trainee')
                    ->latest()
                    ->where('end_date', '<=', Carbon::today());
            })
            ->orWhere('employment_status', 'Probationary')
            ->whereHas('employeeStatus', function($query) {
                $query
                    ->where('status', 'Probationary')
                    ->latest()
                    ->where('end_date', '<=', Carbon::today());
            });
            $others = Employee::with($this->empWith())
            ->whereNotIn('id', (clone $tbl)->pluck('id'));
            $employees = (clone $tbl)->union($others)->get();

            return $this->ForQuery($employees);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function ForQuery($tbl)
    {
        $retVal = [];
        foreach ($tbl as $item) {
            $item->chk = false;
            $item->to_promote = 'no';

            $user_role = User::with(['roles'])
                ->where('employee_id', $item->id)
                ->first();
            $item->roles  = $user_role->roles;
            $emp_stat_hist = employee_status_history::where('employee_id', $item->id)
                ->latest()
                ->first();
            if (@isset($emp_stat_hist->start_date)) {
                $item->start_date = $emp_stat_hist->start_date;
                $item->end_date = $emp_stat_hist->end_date;

                if ($emp_stat_hist->end_date != null && $emp_stat_hist->end_date <= Carbon::today())
                    $item->to_promote = 'yes';
            } else {
                $item->start_date = null;
                $item->end_date = null;
            }

            $app_count = Approver::where('employee_id', $item->id)->count();
            if ($app_count == 0) $item->as_approver = 0;
            else $item->as_approver = 1;

            $c = collect();
            $c->put("sample", "true");
            foreach ($user_role->roles as $role) {
                $c->put($role->name, true);
            }
            $item->roleList = $c;


            array_push($retVal, $item);
        }

        //             OK NANI SIYA
        //             $tbl = DB::connection('sqlsrv')->select("select *
        //   from [FASSQL].[dbo].[User]");
        //             return $this->convert_from_latin1_to_utf8_recursively($tbl);

        return $retVal;
    }
    public function empWith()
    {
        return [
            'user',
            'deduction.type',
            'earning.type',
            'group',
            'rate',
            'position',
            'branch',
            'department',
            'payslip.pay_period',
            'employeeStatus',
            'approver',
        ];
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $hired = new Carbon($request->date_hired);
            $emp = new stdClass();

            if (!isset($request->daily_rate)) {
                $emp = Employee::create($request->all());
            } else {
                $rate_id = DB::table('rates')->insertGetId([
                    'name' => str_replace( ',', '', number_format($request->daily_rate, 2)),
                    'daily_rate' => $request->daily_rate,
                    'sss_deduction' => $request->sss,
                    'phic_deduction' => $request->phic,
                    'hdmf_deduction' => $request->hdmf,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $emp = Employee::create($request->except('rate_id') + [
                    'rate_id' => $rate_id
                ]);
            }


            $email = $hired->format('Ymd') . $emp->id;

            $user = User::create([
                'employee_id' => $emp->id,
                'email' => $email,
                'password' => bcrypt("123456789"),
                'remember_token' => str_random(10)
             ]);
             $emp_status = employee_status_history::create([
                'employee_id' => $emp->id,
                'status' => $request->employment_status,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new User with ID: " . $user->id . "\nDetails: " .  $emp .
                "\nCreate new employee_status with ID: " . $emp_status->id . "\nDetails: " . $emp_status
            );
            DB::commit();
            return json_encode([
                'items' => $this->index(),
                'rates' => Rate::all()
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
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
        $tbl = Employee::where("id", $id)->get();

        return response()->json($tbl);
    }
    public function edit(Employee $Employee)
    {
        //
    }
    public function update(Request $request, $id)
    {
        // return $request;
        try {
            DB::beginTransaction();
            /* $cmd  = Employee::findOrFail($id);
            $logFrom = $cmd->replicate();
            $input = $request->all();
            $cmd->fill($input)->save();
            $logTo = $cmd; */
            $changes = $request->edits;
            $logFrom = "";
            $logTo = "";
            $emp_update = [];
            $emp_query = Employee::where('id', $id);
            $rate_id = 0;

            if (isset($request->daily_rate)) {
                $rate_id = DB::table('rates')->insertGetId([
                    'name' => number_format($request->daily_rate, 2),
                    'daily_rate' => number_format($request->daily_rate, 2),
                    'sss_deduction' => number_format($request->sss, 2),
                    'phic_deduction' => number_format($request->phic, 2),
                    'hdmf_deduction' => number_format($request->hdmf, 2),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            foreach ($changes as $key => $value) {
                if ($value == 'employment_status') {
                    if ($request->employment_status != 'Trainee' &&
                        $request->employment_status != 'Probationary') {
                        $request->end_date = null;
                    }
                    employee_status_history::create([
                        'employee_id' => $id,
                        'status' => $request->employment_status,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date
                    ]);
                }
                foreach ($request->toArray() as $key2 => $value2) {
                    if ($value == 'daily_rate') {
                        $logFrom .= $value . ": " . (clone $emp_query)->value('rate_id') . ", ";
                        if (!isset($request->daily_rate))
                            $rate_id = $request->rate_id;
                        $emp_update['rate_id'] = $rate_id;
                        $logTo .= "rate_id: " . $rate_id . ", ";
                        break;
                    } elseif ($value == $key2) {
                        $logFrom .= $value . ": " . (clone $emp_query)->value($value) . ", ";
                        $emp_update[$value] = $value2;
                        $logTo .= $value . ": " . $value2 . ", ";
                    }
                }
            }
            tap((clone $emp_query))->update($emp_update)->first();

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "update",
                "message",
                "Update Employee with ID: " . $id . "\nFrom: {" . substr($logFrom, 0, -2) . "}\nTo: {" .
                    substr($logTo, 0, -2) . "}"
            );

            DB::commit();
            return json_encode([
                'items' => $this->index(),
                'rates' => Rate::all()
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
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
        $value = explode(",", $id);
        $bioID = $value[0];
        $user_id = $value[1];
        $user_name = $value[2];

        try {
            //destroy also the user
            $tbl1 = Employee::findOrFail($bioID);
            $tbl2 = User::where('employee_id', $bioID)->first();
            User::where('employee_id', $bioID)->delete();
            Employee::destroy($bioID);
            employee_status_history::where('employee_id', $bioID)->delete();

            \Logger::instance()->log(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "message",
                "delete Employee with ID: " . $bioID .
                    "\nOld Employee: " . $tbl1 .
                    "\nOld User: " . $tbl2
            );

            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $user_id,
                $user_name,
                $this->cname,
                "destroy",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function updateRoles(Request $request)
    {
        try {
            $id = $request->id;
            DB::table('role_user')->where('user_id', $id)->delete();
            $roles = [
                "create_employee",
                "update_employee",
                "delete_employee",
                "update_RAlog",
                "view_leave",
                'create_leave',
                'update_leave',
                'delete_leave',
                'view_dtr',
                'create_dtr',
                'update_dtr',
                'delete_dtr',
                'view_approver',
                'create_approver',
                'update_approver',
                'delete_approver',
                'view_payslip',
                'create_payslip',
                'update_payslip',
                'delete_payslip',
                'create_group',
                'update_group',
                'delete_group',
                'create_position',
                'update_position',
                'delete_position',
                'create_department',
                'update_department',
                'delete_department',
                'create_pay_period',
                'update_pay_period',
                'delete_pay_period',
                'create_rate',
                'update_rate',
                'delete_rate',
                'create_branch',
                'update_branch',
                'delete_branch',
                'create_calendar',
                'update_calendar',
                'delete_calendar',
                'manage_leave',
                'operator',
                'hr',
                'employee',
                'admin',
                'rm',
                'network',
                'role',
                'earnings',
                'deduction',
            ];
            $x = 0;
            $roletemp = [];
            $data = "";
            foreach ($roles as $role) {
                $x++;
                if (isset($request->roles[$role])) {
                    if ($request->roles[$role]) {
                        $temp = ['user_id' => $id, 'role_id' => $x];
                        array_push($roletemp, $temp);
                        $data .= $x . " ";
                    }
                }
            }
            DB::table('role_user')->insert($roletemp);

            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateRoles",
                "Message",
                "Update roles for user ID: " . $id . "\nNew roles: " . $data
            );
            return $this->index();
        } catch (\Exception $ex) {
            \Logger::instance()->logError(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "updateRoles",
                "Error",
                $ex->getMessage()
            );
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function multipleFilter(Request $request)
    {
        try {
            $data = (object) $request->data;

            $tbl = Employee::with([
                'user', 'deduction.type', 'earning.type', 'group', 'rate',
                'position', 'branch', 'department', 'payslip.pay_period'
            ]);

            if ($request->group)
                $tbl->where("group_id", $data->group_id);
            if ($request->rate)
                $tbl->where("rate_id", $data->rate_id);
            if ($request->position)
                $tbl->where("position_id", $data->position_id);
            if ($request->branch)
                $tbl->where("branch_id", $data->branch_id);
            if ($request->department)
                $tbl->where("department_id", $data->department_id);
            if ($request->employment_status)
                $tbl->where("employment_status", 'like', '%' . $data->employment_status . '%');
            if ($request->first_name)
                $tbl->where("first_name", 'like', '%' . $data->first_name . '%');
            if ($request->last_name)
                $tbl->where("last_name", 'like', '%' . $data->last_name . '%');
            if ($request->middle_name)
                $tbl->where("middle_name", 'like', '%' . $data->middle_name . '%');
            if ($request->gender)
                $tbl->where("gender", $data->gender);
            else if (!$request->group && !$request->rate && !$request->position && !$request->branch && !$request->department &&
                !$request->employment_status && !$request->first_name && !$request->last_name && !$request->middle_name && !$request->gender)
                return $this->index();

            return $this->ForQuery($tbl->get());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function sendCredentials(Request $request)
    {
        //message
        if (true) {
            $message = "
            <html>
            <head>
            </head>
            <body>
            <p>Hi " . $request->fullname . ",</p>
            <p>Good day!</p>
            <br />
            <p>THIS IS AN AUTOMATIC GENERATED E-MAIL FROM HRMESS. </p>
            <p>These are your credentials</p>
            <p>Username : <b>" . $request->email . "</b></p>
            <p>Password : <b>123456789</b></p>
            <p>Please change your password after you login.</p>
            <p>Click <a href='https://hrmess.dctechmicro.com'>here</a> to log in.</p>

            <br />

            <p>Thank you!</p>
            <p>PLEASE DO NOT REPLY TO THIS E-MAIL</p>
            </body>
            </html>";
        }


        return \Logger::instance()->mailer(
            "Your Credentials in HRMESS",
            $message,
            $request->user_email,
            $request->user_name,
            $request->sendTo,
            $request->CCto
        );
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
    public function getToPromote() {
        return Employee::with($this->empWith())
            ->where('employment_status', 'Trainee')
            ->whereHas('employeeStatus', function($query) {
                $query
                    ->where('status', 'Trainee')
                    ->latest()
                    ->where('end_date', '<=', Carbon::today());
            })
            ->orWhere('employment_status', 'Probationary')
            ->whereHas('employeeStatus', function($query) {
                $query
                    ->where('status', 'Probationary')
                    ->latest()
                    ->where('end_date', '<=', Carbon::today());
            })
            ->count();
    }
    public function checkRate($rate) {
        $rate_count = DB::table('rates')
            ->where('id', $rate)
            ->count();
        return json_encode([
            'rate_count' => $rate_count,
            'benefits' => Rate::where('id', $rate)->get()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class EmployeeController extends Controller
{
    private $cname = "EmployeeController";
    public function index()
    {
        try {
            $tbl = Employee::with(['user', 'deduction.type', 'earning.type', 'group', 'rate', 'position', 'branch', 'department', 'payslip.pay_period'])
                ->get();

            return $this->ForQuery($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    public function ForQuery($tbl)
    {
        $retVal = [];
        foreach ($tbl as $item) {
            $item->chk = false;

            $user_role = User::with(['roles'])
                ->where('employee_id', $item->id)
                ->first();
            $item->roles  = $user_role->roles;

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
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $hired = new Carbon($request->date_hired);

            $emp = Employee::create($request->all());

            $email = $hired->format('Ymd') . $emp->id;

            $user = DB::table('users')->insert([
                [
                    'employee_id' => $emp->id,
                    'email' => $email,
                    'password' => bcrypt("123456789"),
                    'remember_token' => str_random(10),
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            ]);
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "message",
                "Create new User: " . $user . "\nEmployee: " .  $emp
            );
            DB::commit();
            return $this->index();
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
        try {
            $cmd  = Employee::findOrFail($id);
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
                "update Employee id " . $id . "\nFrom: " . $logFrom . "\nTo: " . $logTo
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
            //destopy also the user
            $tbl1 = Employee::findOrFail($id);
            $tbl2 = User::where('employee_id', $id)->first();
            User::where('employee_id', $id)->delete();
            Employee::destroy($id);

            \Logger::instance()->log(
                Carbon::now(),
                "",
                "",
                $this->cname,
                "destroy",
                "message",
                "delete Employee id " . $id .
                    "\nOld Employee: " . $tbl1 .
                    "\nOld User: " . $tbl2
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
            foreach ($roles as $role) {
                $x++;
                if (isset($request->roles[$role])) {
                    if ($request->roles[$role]) {
                        $temp = ['user_id' => $id, 'role_id' => $x];
                        array_push($roletemp, $temp);
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
                "Update Role User ID: " . $id
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
                $tbl->where("gender", 'like', '%' . $data->gender . '%');


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
            <p>Click <a href='http://hrmess.dctechmicro.com'>here</a> to log in.</p>

            <br />

            <p>Thank you!</p>
            <p>PLEASE DO NOT REPLY TO THIS E-MAIL</p>
            </body>
            </html>";
        }
        //format sa send to sa front ni
        //   item.sendTo = [
        //       {
        //         email: "cnc@dctechmicro.com",
        //         name: "Credits and Collection"
        //       }
        //     ];

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


}

<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
         $tbl = User::with(['roles'])->get();

        $users = [];

        foreach ($tbl as $user) {
            $c = collect();
            $c->put("sample", "true");
            foreach ($user->roles as $role) {
                $c->put($role->name, true);
            }
            $user->roleList = $c;
            array_push($users, $user);
        }

        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();

        if (!empty($user)) {
            $user_roles = [];
            $user_roles = collect($user_roles);

            foreach ($roles as $role) {
                if (DB::table('role_user')
                    ->where('role_id', $role->id)
                    ->where('user_id', $id)
                    ->exists()
                ) {
                    $user_roles->put($role->name, true);
                } else {
                    $user_roles->put($role->name, false);
                }
            }

            $user_roles = $user_roles->all();

            $user = collect($user);
            $user->put('roles', $user_roles);
            $user = $user->all();

            return response()->json($user);
        }

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function store(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();

        if (empty($user)) {
            DB::table('users')->insert([
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'remember_token' => str_random(10),
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            ]);

            return response()->json($this->index());
        } else {
            return response()->json(['error' => 'Email already exists!'], 500);
        }
    }

    public function update(Request $request, $id)
    {

        if (empty($request->password)) {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        } else {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        }

        if (!empty($request->roles)) {
            DB::table('role_user')->where('user_id', $id)->delete();
            $request->roles = (object) $request->roles;
            $this->setRoles($request->roles, $id);
        }

        return response()->json($this->index());
    }

    public function ResetPassword(Request $request)
    {
        try {
            DB::table('users')
                ->where('employee_id', $request->id)
                ->update([
                    'password' => bcrypt($request->password),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
            return "ok";
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function showSearch(Request $request)
    {
        $users = User::where('name', 'like', '%' . $request->user . '%')->get();
        return response()->json($users);
    }

    public function getUser($email)
    {
        $user = User::with(['employee', 'approver'])
            ->where('email', '=', $email)->first();
        return response()->json($user);
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
            ];
            $x = 0;
            foreach ($roles as $role) {
                $x++;
                if (isset($request->roles[$role])) {
                    if ($request->roles[$role])
                        DB::table('role_user')->insert(
                            ['user_id' => $id, 'role_id' => $x]
                        );
                }
            }
            return $this->index();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function getApplication($type, $approve_id)
    {
    }

    public function getRole($id)
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();

        if (!empty($user)) {
            $user_roles = [];
            $user_roles = collect($user_roles);

            foreach ($roles as $role) {
                if (DB::table('role_user')
                    ->where('role_id', $role->id)
                    ->where('user_id', $id)
                    ->exists()
                ) {
                    $user_roles->put($role->name, true);
                } else {
                    $user_roles->put($role->name, false);
                }
            }

            $user_roles = $user_roles->all();

            $user = collect($user);
            $user->put('roles', $user_roles);
            $user = $user->all();

            return response()->json($user);
        }

        return response()->json(['error' => 'Resource not found!'], 404);
    }
}

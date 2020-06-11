<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class EmployeeController extends Controller
{
    public function index()
    {
        try {

            $tbl = Employee::with(['user', 'group', 'rate', 'position', 'branch', 'department'])
                ->get();

            $retVal = [];
            foreach ($tbl as $item) {
                $item->chk = false;

                $user_role = User::with(['roles'])
                ->where('employee_id' , $item->id)
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
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $hired = new Carbon($request->date_hired);

            $emp = Employee::create($request->all());

            $email = $hired->format('Ymd') . $emp->id;

            DB::table('users')->insert([
                [
                    'employee_id' => $emp->id,
                    'email' => $email,
                    'password' => bcrypt("123456789"),
                    'remember_token' => str_random(10),
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            ]);

            return $this->index();
        } catch (\Exception $ex) {
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
            Employee::destroy($id);

            return $this->index();
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

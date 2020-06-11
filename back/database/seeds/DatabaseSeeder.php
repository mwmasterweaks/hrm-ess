<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement("ALTER TABLE employees AUTO_INCREMENT = 11000");

        App\Employee::create([
            'id' => '1',
            'group_id' => '1',
            'rate_id' => '1',
            'position_id' => '1',
            'branch_id' => '1',
            'department_id' => '1',
            'employment_status' => 'Regular',
            'first_name' => 'master',
            'last_name' => 'weak',
            'date_hired' => '1994-01-28',
        ]);

        App\User::create([
            'employee_id' => '1',
            'username' => 'mwmasterweaks',
            'password' => bcrypt('pesotko94'),
            'remember_token' => str_random(10),
        ]);

        App\Role::create(['name' => 'create_employee']); //1
        App\Role::create(['name' => 'update_employee']); //2
        App\Role::create(['name' => 'delete_employee']); //3
        App\Role::create(['name' => 'update_RAlog']); //4
        App\Role::create(['name' => 'view_leave']); //5
        App\Role::create(['name' => 'create_leave']); //6
        App\Role::create(['name' => 'update_leave']); //7
        App\Role::create(['name' => 'delete_leave']); //8
        App\Role::create(['name' => 'view_dtr']); //9
        App\Role::create(['name' => 'create_dtr']); //10
        App\Role::create(['name' => 'update_dtr']); //11
        App\Role::create(['name' => 'delete_dtr']); //12
        App\Role::create(['name' => 'view_approver']); //13
        App\Role::create(['name' => 'create_approver']); //14
        App\Role::create(['name' => 'update_approver']); //15
        App\Role::create(['name' => 'delete_approver']); //16
        App\Role::create(['name' => 'view_payslip']); //17
        App\Role::create(['name' => 'create_payslip']); //18
        App\Role::create(['name' => 'update_payslip']); //19
        App\Role::create(['name' => 'delete_payslip']); //20
        App\Role::create(['name' => 'create_group']); //21
        App\Role::create(['name' => 'update_group']); //22
        App\Role::create(['name' => 'delete_group']); //23
        App\Role::create(['name' => 'create_position']); //24
        App\Role::create(['name' => 'update_position']); //25
        App\Role::create(['name' => 'delete_position']); //26
        App\Role::create(['name' => 'create_department']); //27
        App\Role::create(['name' => 'update_department']); //28
        App\Role::create(['name' => 'delete_department']); //29
        App\Role::create(['name' => 'create_pay_period']); //30
        App\Role::create(['name' => 'update_pay_period']); //31
        App\Role::create(['name' => 'delete_pay_period']); //32
        App\Role::create(['name' => 'create_rate']); //33
        App\Role::create(['name' => 'update_rate']); //34
        App\Role::create(['name' => 'delete_rate']); //35
        App\Role::create(['name' => 'create_branch']); //36
        App\Role::create(['name' => 'update_branch']); //37
        App\Role::create(['name' => 'delete_branch']); //38
        App\Role::create(['name' => 'create_calendar']); //39
        App\Role::create(['name' => 'update_calendar']); //40
        App\Role::create(['name' => 'delete_calendar']); //41
        App\Role::create(['name' => 'manage_leave']); //42
        App\Role::create(['name' => 'operator']); //43
        App\Role::create(['name' => 'hr']); //44
        App\Role::create(['name' => 'employee']); //45
        App\Role::create(['name' => 'admin']); //46
        App\Role::create(['name' => 'rm']); //47
        App\Role::create(['name' => 'network']); //48
        App\Role::create(['name' => 'role']); //49

        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 1, 'role_id' => 2],
            ['user_id' => 1, 'role_id' => 3],
            ['user_id' => 1, 'role_id' => 4],
            ['user_id' => 1, 'role_id' => 5],
            ['user_id' => 1, 'role_id' => 6],
            ['user_id' => 1, 'role_id' => 7],
            ['user_id' => 1, 'role_id' => 8],
            ['user_id' => 1, 'role_id' => 9],
            ['user_id' => 1, 'role_id' => 10],
            ['user_id' => 1, 'role_id' => 11],
            ['user_id' => 1, 'role_id' => 12],
            ['user_id' => 1, 'role_id' => 13],
            ['user_id' => 1, 'role_id' => 14],
            ['user_id' => 1, 'role_id' => 15],
            ['user_id' => 1, 'role_id' => 16],
            ['user_id' => 1, 'role_id' => 17],
            ['user_id' => 1, 'role_id' => 18],
            ['user_id' => 1, 'role_id' => 19],
            ['user_id' => 1, 'role_id' => 20],
            ['user_id' => 1, 'role_id' => 21],
            ['user_id' => 1, 'role_id' => 22],
            ['user_id' => 1, 'role_id' => 23],
            ['user_id' => 1, 'role_id' => 24],
            ['user_id' => 1, 'role_id' => 25],
            ['user_id' => 1, 'role_id' => 26],
            ['user_id' => 1, 'role_id' => 27],
            ['user_id' => 1, 'role_id' => 28],
            ['user_id' => 1, 'role_id' => 29],
            ['user_id' => 1, 'role_id' => 30],
            ['user_id' => 1, 'role_id' => 31],
            ['user_id' => 1, 'role_id' => 32],
            ['user_id' => 1, 'role_id' => 33],
            ['user_id' => 1, 'role_id' => 34],
            ['user_id' => 1, 'role_id' => 35],
            ['user_id' => 1, 'role_id' => 36],
            ['user_id' => 1, 'role_id' => 37],
            ['user_id' => 1, 'role_id' => 38],
            ['user_id' => 1, 'role_id' => 39],
            ['user_id' => 1, 'role_id' => 40],
            ['user_id' => 1, 'role_id' => 41],
            ['user_id' => 1, 'role_id' => 42],
            ['user_id' => 1, 'role_id' => 43],
            ['user_id' => 1, 'role_id' => 44],
            ['user_id' => 1, 'role_id' => 45],
            ['user_id' => 1, 'role_id' => 46],
            ['user_id' => 1, 'role_id' => 47],
            ['user_id' => 1, 'role_id' => 48],
            ['user_id' => 1, 'role_id' => 49],
        ]);

        App\leave_type::create(['name' => 'Leave With Out Pay']);
        App\leave_type::create(['name' => 'Maternity Leave']);
        App\leave_type::create(['name' => 'Emergency Leave']);
        App\leave_type::create(['name' => 'Bereavement Leave']);
        App\leave_type::create(['name' => 'Vacation Leave']);
        App\leave_type::create(['name' => 'Sick Leave']);

        DB::statement("ALTER TABLE `leaves` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `change_rest_days` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `change_shifts` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `manual_attendances` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `missing_time_logs` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `official_businesses` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
        DB::statement("ALTER TABLE `over_times` CHANGE `attachment` `attachment` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'noattachment.png';");
    }
}

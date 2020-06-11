<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->integer('rate_id');
            $table->integer('position_id');
            $table->integer('branch_id');
            $table->integer('department_id');
            $table->string('employment_status');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->date('date_hired');
            $table->string('img')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('tel_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email1')->nullable();
            $table->string('email2')->nullable();
            $table->string('provincial_address')->nullable();
            $table->string('provincial_tel_no')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('sss_no')->nullable();
            $table->string('pag_ibig')->nullable();
            $table->string('prc_license')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->timestamps();
        });


        DB::statement("ALTER TABLE employees AUTO_INCREMENT = 11000");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

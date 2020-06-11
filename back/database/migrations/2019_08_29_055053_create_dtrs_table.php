<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDtrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtrs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->integer('pay_period_id');
            $table->date('work_date');
            $table->string('day');
            $table->datetime('shift_sched_in');
            $table->datetime('shift_sched_out');
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->boolean('is_rest_day')->tinyInteger(5)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dtrs');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOverTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('over_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('reference_no');
            $table->string('type');
            $table->date('work_date');
            $table->string('shift');
            $table->datetime('time_in');
            $table->datetime('time_out');
            $table->string('with_break')->nullable();
            $table->decimal('break_hours')->default(0.00);
            $table->decimal('total_hours')->default(0.00);
            $table->string('reason');
            $table->string('attachment')->default('noattachment.png');
            $table->date('date_filed');
            $table->string('status')->default('Pending');
            $table->string('status_paid')->default('no');
            $table->string('remarks')->nullable();
            $table->integer('approve_level');
            $table->date('approve_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('over_times');
    }
}

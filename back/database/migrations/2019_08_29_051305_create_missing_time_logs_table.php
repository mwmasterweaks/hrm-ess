<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissingTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missing_time_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('reference_no');
            $table->string('work_date');
            $table->string('shift');
            $table->datetime('time_in');
            $table->datetime('time_out');
            $table->string('reason');
            $table->string('attachment')->default('noattachment.png');
            $table->date('date_filed');
            $table->string('status')->default('Pending');
            $table->string('remarks')->nullable();
            $table->integer('approve_level');
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
        Schema::dropIfExists('missing_time_logs');
    }
}

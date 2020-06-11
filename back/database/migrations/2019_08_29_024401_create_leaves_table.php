<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('leave_type_id');
            $table->string('reference_no');
            $table->date('date_from');
            $table->date('date_to');
            $table->decimal('total_days');
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
        Schema::dropIfExists('leaves');
    }
}

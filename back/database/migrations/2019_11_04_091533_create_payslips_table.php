<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('employee_id');
            $table->integer('pay_period_id');
            $table->decimal('month_pay')->nullable();
            $table->decimal('tax_refund')->nullable();
            $table->decimal('vl_conversion')->nullable();
            $table->decimal('incentive')->nullable();
            $table->decimal('other_bunos')->nullable();
            $table->decimal('uniform')->nullable();
            $table->decimal('sss')->nullable();
            $table->decimal('phic')->nullable();
            $table->decimal('hdmf')->nullable();
            $table->decimal('wtx')->nullable();
            $table->decimal('sss_loan')->nullable();
            $table->decimal('hdmf_loan')->nullable();
            $table->decimal('cp_charge')->nullable();
            $table->decimal('cash_advance')->nullable();
            $table->decimal('other_deduction')->nullable();
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
        Schema::dropIfExists('payslips');
    }
}

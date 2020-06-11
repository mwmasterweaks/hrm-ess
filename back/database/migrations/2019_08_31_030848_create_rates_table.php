<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('daily_rate');
            $table->decimal('regular_ot_rate');
            $table->decimal('holiday_ot_rate');
            $table->decimal('regular_holiday_rate');
            $table->decimal('special_holiday_rate');
            $table->decimal('night_differencial');
            $table->decimal('undertime_rate');
            $table->decimal('late_rate');
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
        Schema::dropIfExists('rates');
    }
}

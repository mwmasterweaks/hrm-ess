<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuggestionCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suggestion_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('suggestion_id');
            $table->integer('user_id');
            $table->string('comment',9000);
            $table->string('status')->default("new");
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
        Schema::dropIfExists('suggestion_comments');
    }
}

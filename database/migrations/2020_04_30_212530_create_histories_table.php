<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained();
            $table->unsignedBigInteger('team_id_1');
            $table->unsignedBigInteger('team_id_2');
            $table->unsignedTinyInteger('score_1');
            $table->unsignedTinyInteger('score_2');
            $table->date('date');
            $table->string('team_name_1');
            $table->string('team_name_2');
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
        Schema::dropIfExists('histories');
    }
}

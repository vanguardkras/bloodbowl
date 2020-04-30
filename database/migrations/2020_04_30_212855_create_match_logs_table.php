<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('round');
            $table->foreignId('team_id_1')->constrained('teams');
            $table->foreignId('team_id_2')->constrained('teams');
            $table->unsignedTinyInteger('score_1');
            $table->unsignedTinyInteger('score_2');
            $table->date('date');
            $table->foreignId('history_id')->constrained();
            $table->boolean('confirmed');
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
        Schema::dropIfExists('match_logs');
    }
}

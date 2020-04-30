<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('info', 1000);
            $table->foreignId('user_id')->constrained();
            $table->unsignedTinyInteger('type');
            $table->json('parameters');
            $table->unsignedTinyInteger('self_confirm');
            $table->unsignedTinyInteger('tops_number');
            $table->unsignedTinyInteger('winner_points');
            $table->unsignedSmallInteger('round');
            $table->date('registration_end');
            $table->unsignedSmallInteger('max_players');
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
        Schema::dropIfExists('competitions');
    }
}

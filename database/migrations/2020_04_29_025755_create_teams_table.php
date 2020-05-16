<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->foreignId('race_id')->constrained()->cascadeOnDelete();
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('competition_id')->nullable();
            $table->unsignedInteger('touchdowns')->default(0);
            $table->unsignedInteger('played')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('draws')->default(0);
            $table->timestamps();
        });

        $bot = new Team;
        $bot->user_id = 1;
        $bot->name = 'BOT';
        $bot->race_id = 13;
        $bot->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $teams = Team::all('logo');
        foreach ($teams as $team) {
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
        }
        Schema::dropIfExists('teams');
    }
}

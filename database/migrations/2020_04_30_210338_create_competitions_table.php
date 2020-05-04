<?php

use App\Models\Competition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
            $table->string('info', 1000)->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('type');
            $table->json('parameters')->nullable();
            $table->unsignedTinyInteger('self_confirm');
            $table->unsignedTinyInteger('tops_number');
            $table->unsignedTinyInteger('winner_points');
            $table->unsignedSmallInteger('round')->default(0);
            $table->date('registration_end');
            $table->unsignedSmallInteger('max_teams')->default(0);
            $table->date('finished')->nullable();
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
        $teams = Competition::all('logo');
        foreach ($teams as $team) {
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
        }
        Schema::dropIfExists('competitions');
    }
}

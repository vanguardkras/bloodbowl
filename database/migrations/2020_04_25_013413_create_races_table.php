<?php

use App\Models\Race;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    public $default_races = [
        ['en' => 'Amazon', 'ru' => 'Амазонки'],
        ['en' => 'Chaos', 'ru' => 'Хаос'],
        ['en' => 'Chaos Dwarfs', 'ru' => 'Гномы Хаоса'],
        ['en' => 'Chaos Renegades', 'ru' => 'Ренегаты Хаоса'],
        ['en' => 'Dark Elves', 'ru' => 'Тёмные Эльфы'],
        ['en' => 'Dwarfs', 'ru' => 'Гномы'],
        ['en' => 'Elves', 'ru' => 'Эльфы'],
        ['en' => 'Goblins', 'ru' => 'Гоблины'],
        ['en' => 'Halflings', 'ru' => 'Халфлинги'],
        ['en' => 'High Elves', 'ru' => 'Высшие Эльфы'],
        ['en' => 'Humans', 'ru' => 'Люди'],
        ['en' => 'Khemri', 'ru' => 'Кхемри'],
        ['en' => 'Lizardmen', 'ru' => 'Лизардмены'],
        ['en' => 'Necromantic', 'ru' => 'Некроманты'],
        ['en' => 'Norse', 'ru' => 'Норсы'],
        ['en' => 'Nurgle', 'ru' => 'Нурглиты'],
        ['en' => 'Ogre', 'ru' => 'Огры'],
        ['en' => 'Orcs', 'ru' => 'Орки'],
        ['en' => 'Skaven', 'ru' => 'Скавены'],
        ['en' => 'Undead', 'ru' => 'Нежить'],
        ['en' => 'Underworld Denizens', 'ru' => 'Подземные Обитатели'],
        ['en' => 'Vampire', 'ru' => 'Вампиры'],
        ['en' => 'Wood Elves', 'ru' => 'Лесные Эльфы'],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 50);
            $table->string('name_ru', 50);
            $table->boolean('is_default')->default(false);
        });

        foreach ($this->default_races as $default_race) {
            $race = new Race;
            $race->name_en = $default_race['en'];
            $race->name_ru = $default_race['ru'];
            $race->is_default = true;
            $race->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('races');
    }
}

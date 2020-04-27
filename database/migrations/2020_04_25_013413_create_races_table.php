<?php

use App\Models\Race;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    public $default_races = [
        ['en' => 'Amazon', 'ru' => 'Амазонки', 'is_default' => true],
        ['en' => 'Bretonians', 'ru' => 'Бретонцы', 'is_default' => false],
        ['en' => 'Chaos', 'ru' => 'Хаос', 'is_default' => true],
        ['en' => 'Chaos Dwarfs', 'ru' => 'Гномы Хаоса', 'is_default' => true],
        ['en' => 'Chaos Renegades', 'ru' => 'Ренегаты Хаоса', 'is_default' => true],
        ['en' => 'Daemons of Khorne', 'ru' => 'Демоны Кхорна', 'is_default' => false],
        ['en' => 'Dark Elves', 'ru' => 'Тёмные Эльфы', 'is_default' => true],
        ['en' => 'Dwarfs', 'ru' => 'Гномы', 'is_default' => true],
        ['en' => 'Elves', 'ru' => 'Эльфы', 'is_default' => true],
        ['en' => 'Goblins', 'ru' => 'Гоблины', 'is_default' => true],
        ['en' => 'Halflings', 'ru' => 'Халфлинги', 'is_default' => true],
        ['en' => 'High Elves', 'ru' => 'Высшие Эльфы', 'is_default' => true],
        ['en' => 'Humans', 'ru' => 'Люди', 'is_default' => true],
        ['en' => 'Human Nobility', 'ru' => 'Благородные Люди', 'is_default' => true],
        ['en' => 'Khemri', 'ru' => 'Кхемри', 'is_default' => true],
        ['en' => 'Lizardmen', 'ru' => 'Лизардмены', 'is_default' => true],
        ['en' => 'Necromantic', 'ru' => 'Некроманты', 'is_default' => true],
        ['en' => 'Norse', 'ru' => 'Норсы', 'is_default' => true],
        ['en' => 'Nurgle', 'ru' => 'Нурглиты', 'is_default' => true],
        ['en' => 'Ogre', 'ru' => 'Огры', 'is_default' => true],
        ['en' => 'Orcs', 'ru' => 'Орки', 'is_default' => true],
        ['en' => 'Pestilent Vermin', 'ru' => 'Чумные Крысы', 'is_default' => true],
        ['en' => 'Skaven', 'ru' => 'Скавены', 'is_default' => true],
        ['en' => 'Slann', 'ru' => 'Слааны', 'is_default' => false],
        ['en' => 'Savage Orcs', 'ru' => 'Дикие Орки', 'is_default' => true],
        ['en' => 'Slayer Hold', 'ru' => 'Гномы убийцы', 'is_default' => true],
        ['en' => 'Undead', 'ru' => 'Нежить', 'is_default' => true],
        ['en' => 'Underworld Denizens', 'ru' => 'Подземные Обитатели', 'is_default' => true],
        ['en' => 'Vampire', 'ru' => 'Вампиры', 'is_default' => true],
        ['en' => 'Wood Elves', 'ru' => 'Лесные Эльфы', 'is_default' => true],
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
            $race->is_default = $default_race['is_default'];
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

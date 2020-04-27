<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->boolean('is_super')->default(false);
            $table->string('vk_id')->nullable();
            $table->timestamps();
        });

        $super = new User;
        $super->username = env('SUPER_USERNAME');
        $super->email = env('SUPER_EMAIL');
        $super->email_verified_at = time();
        $super->password = Hash::make(env('SUPER_PASSWORD'));
        $super->is_super = true;
        $super->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('login/vkontakte', 'Auth\LoginController@redirectToProvider');
Route::get('login/vkontakte/callback', 'Auth\LoginController@handleProviderCallback');

Route::resource('races', 'SuperAdmin\RaceController')->middleware('is_super');

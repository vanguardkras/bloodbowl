<?php

use App\Http\Controllers\Auth\VkAuthController;
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

Route::get('/', 'PageController@main');

Auth::routes(['verify' => true]);

Route::get('login/vkontakte', 'Auth\VkAuthController@vkLogin');
Route::get(VkAuthController::REDIRECT_URI, 'Auth\VkAuthController@vkCallback');

Route::resource('races', 'SuperAdmin\RaceController')->middleware('is_super');

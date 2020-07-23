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

// Pages route
Route::get('/', 'PageController@main');
Route::get('competitions/{competition}/show', 'PageController@competition');
Route::get('user/{user}', 'PageController@user');

//Auth routes
Auth::routes(['verify' => true]);
Route::get('login/vkontakte', 'Auth\VkAuthController@vkLogin');
Route::get(VkAuthController::REDIRECT_URI, 'Auth\VkAuthController@vkCallback');

// Super Admin routes
Route::resource('races', 'SuperAdmin\RaceController')->middleware('is_super');

// Coaches routes
Route::middleware('auth')->group(function () {
    Route::get('profile', 'UserController@profile')->middleware('auth');
    Route::patch('profile_data_update', 'UserController@updateData');
    Route::patch('change_password', 'UserController@changePassword');
    Route::patch('become_commissioner', 'UserController@becomeCommissioner');
    Route::post('competitions/{competition}/register_team', 'PageController@registerTeam');
    Route::post('results/{competition}', 'PageController@recordResults');
    Route::post('results/{match_log}/confirm', 'PageController@confirmResults');
    Route::post('results/{match_log}/reject', 'PageController@rejectResults');

    // Teams management routes
    Route::resource('teams', 'TeamController')->except('edit');

    // Ajax Add Results
    Route::get('is_commissioner/{competition}', 'AddResultsAjaxController@isCommissioner');
    Route::get('get_team/{competition}', 'AddResultsAjaxController@getTeam');
    Route::get('get_possible_opponents/{competition}/{team}', 'AddResultsAjaxController@getPossibleOpponents');
});

//Competitons routes
Route::middleware('commissioner')->group(function () {
    Route::resource('competitions', 'CompetitionController');
    Route::post('register/{competition}/{team}', 'CompetitionController@registerTeam');
    Route::post('competitions/{competition}/next_round', 'CompetitionController@nextRound');
});


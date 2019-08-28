<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/events', 'EventsController@getUpcoming');
Route::get('/events/date/{date}', 'EventsController@getByDate');
Route::get('/events/week/{week}/{year}', 'EventsController@getByWeek');
Route::post('/events', 'EventsController@store');
Route::put('/events/{event}', 'EventsController@update');
Route::delete('/events/{event}', 'EventsController@destroy');

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');
});
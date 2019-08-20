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

Route::get('/events', 'EventsController@getUpcoming');
Route::get('/events/date/{date}', 'EventsController@getByDate');
Route::get('/events/week/{week}/{year}', 'EventsController@getByWeek');
Route::post('/events', 'EventsController@store');
Route::put('/events/{event}', 'EventsController@update');
Route::delete('/events/{event}', 'EventsController@destroy');
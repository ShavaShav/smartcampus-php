<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function ()
{
	Route::post('user/login', 'Auth\AuthController@login');
	Route::post('user/register', 'Auth\AuthController@register');

	Route::get('events', 'EventController@index');
	Route::get('events/{event}', 'EventController@show');

	// Authenticated routes
	Route::group(['middleware' => ['jwt.auth']], function()
	{
	    Route::post('user/logout', 'Auth\AuthController@logout');
	    Route::get('user', 'Auth\AuthController@show');

	    Route::post('events', 'EventController@store');
	    Route::delete('events/{event}', 'EventController@destroy');
	});
});

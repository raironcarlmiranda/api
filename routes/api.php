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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return auth('api')->user();
});

Route::post('/login', 'API\LoginController@login');
//Route::middleware('auth:api')->get('/users', 'API\UserController@index');
Route::middleware('auth:api')->get('/sqp', 'API\AssuredController@index');
Route::middleware('auth:api')->post('/sqp', 'API\AssuredController@store');

Route::group(['prefix' => 'v1'], function () {
    Route::middleware('auth:api')->post('/payassured', 'API\AssuredController@store_payassured');
    Route::middleware('auth:api')->post('/kasambahay', 'API\AssuredController@store_kasambahay');
    Route::middleware('auth:api')->post('/pet', 'API\AssuredController@store_pet');
    Route::middleware('auth:api')->post('/mom', 'API\AssuredController@store_mom');
    Route::middleware('auth:api')->post('/rider', 'API\AssuredController@store_rider');
    Route::middleware('auth:api')->post('/game', 'API\AssuredController@store_game');
    Route::middleware('auth:api')->post('/covid', 'API\AssuredController@store_covid');
});
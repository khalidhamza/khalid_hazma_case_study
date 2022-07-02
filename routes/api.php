<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'App\Http\Controllers\Api'], function(){
    Route::fallback('ApiController@fallbackRoute');

    Route::group(['prefix' => 'v1', 'middleware' => ['verifyApiToken']], function(){
        // user auth
        Route::post('auth/register', 'UserController@register');
        Route::post('auth/login', 'UserController@login');
    
    });
});

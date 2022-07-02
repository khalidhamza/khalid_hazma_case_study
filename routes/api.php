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

    Route::group(['prefix' => 'v1'], function(){
        // user auth
        Route::post('auth/register', 'UserController@register');
        Route::post('auth/login', 'UserController@login');
        
        // products
        Route::post('products', 'ProductController@create');
        Route::get('products', 'ProductController@get');
        Route::get('products/{id}', 'ProductController@details');
        Route::delete('products/{id}', 'ProductController@delete');

        // cart
        Route::group(['middleware' => 'identifyUser'], function(){
            Route::post('cart', 'CartController@create');
            Route::put('cart/{id}', 'CartController@update');
            Route::delete('cart/{id}', 'CartController@delete');
            Route::get('cart', 'CartController@get');
        });
    });
});

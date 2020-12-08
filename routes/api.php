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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    // user
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

// color
Route::post('/color', 'ColorController@store');
Route::put('/color/{id}', 'ColorController@update');
Route::get('/colors', 'ColorController@all');
Route::delete('/color/{id}', 'ColorController@delete');

// product
Route::put('/product/{id}', 'ProductController@update');
Route::post('/product', 'ProductController@store');
Route::get('/products', 'ProductController@all');
Route::get('/product/{id}', 'ProductController@getOneProduct');
Route::delete('/product/{id}', 'ProductController@delete');

// user
Route::get('/user/login', 'UserController@login')->name('login');
Route::post('/auth/register', 'UserController@register');
Route::delete('/user/email', 'UserController@remove');
Route::put('/user/{id}', 'UserController@store');
Route::delete('/user/{id}', 'UserController@delete');
Route::get('/users', 'UserController@all');

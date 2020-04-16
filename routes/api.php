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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login')->name('auth.login');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'AuthController@logout')->name('auth.logout');
        Route::get('info', 'AuthController@info')->name('auth.info');
        Route::post('change-password', 'AuthController@changePassword')->name('auth.change-password');
        Route::post('update-profile', 'AuthController@updateProfile')->name('auth.update-profile');
    });
});

Route::group(
    [
        'middleware' => 'auth:api',
        'prefix' => 'users'
    ], function () {
        Route::get('', 'UserController@index')
            ->name('users.index');
        Route::post('', 'UserController@store')
            ->name('users.store');
        Route::get('{user}', 'UserController@show')
            ->name('users.show');
        Route::put('{user}', 'UserController@update')
            ->name('users.update');
        Route::post('{user}/change-password', 'UserController@changePassword')
            ->name('users.change-password');
        Route::delete('{user}', 'UserController@destroy')
            ->name('users.destroy');
    }
);

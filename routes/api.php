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

Route::group(
    [
        'prefix' => 'users'
    ], function () {
        Route::get('', 'UserController@index')
            ->name('user-index');
        Route::post('', 'UserController@store')
            ->name('user-store');
        Route::get('{user}', 'UserController@show')
            ->name('user-show');
        Route::post('{user}', 'UserController@update')
            ->name('user-update');
        Route::post('{user}/change-password', 'UserController@changePassword')
            ->name('user-change-password');
        Route::delete('{user}', 'UserController@destroy')
            ->name('user-destroy');
    }
);

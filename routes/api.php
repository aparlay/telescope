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


Route::prefix('v1')->middleware('api')->group(function () {
    Route::prefix('media')->group(function () {
        Route::match(['head', 'get'], '/', 'MediaController@index')->name('media.list');
        Route::match(['head', 'get'], '/{media}', 'MediaController@show')->name('media.show');
        Route::match(['get', 'post'], '/upload', 'MediaController@upload')->name('media.upload');
        Route::post('/', 'MediaController@store')->name('media.create');
        Route::delete('/{media}', 'MediaController@destroy')->name('media.delete');
        Route::match(['put', 'patch'], '/{media}', 'MediaController@update')->name('media.update');

        Route::put('/{media}/like', 'MediaLikeController@store')->name('media.like');
        Route::delete('/{media}/like', 'MediaLikeController@destroy')->name('media.unlike');

        Route::post('/{media}/report', 'ReportController@media')->name('media.report');
    });

    Route::prefix('user')->group(function () {
        Route::get('/{type}', 'UserController@index')
            ->where(['type' => '(likes|blocks|followers|followings|hashtags)']);

        Route::put('/{user}/block', 'BlockController@store');
        Route::delete('/{user}/block', 'BlockController@destroy');
        Route::put('/{user}/follow', 'FollowController@store');
        Route::delete('/{user}/follow', 'FollowController@destroy');

        Route::post('/{user}/report', 'ReportController@user');

        Route::get('/{user}/media', 'MediaController@listByUser');
        Route::get('/{user}', 'UserController@show');
    });

    Route::prefix('me')->group(function () {
        Route::get('/', 'UserController@me');
        Route::match(['put', 'patch'], '/', 'UserController@update');
        Route::delete('/', 'UserController@deactive');
        Route::get('/token', 'UserController@token');
    });

    Route::prefix('alert')->group(function () {
        Route::get('/', 'UserController@me');
        Route::match(['put', 'patch'], '/', 'UserController@update');
        Route::delete('/', 'UserController@deactive');
        Route::get('/token', 'UserController@token');
    });

    Route::match(['put', 'patch'], '/{alert}', 'AlertController@update');
    Route::match(['put', 'patch'], '/change-password', 'UserController@changePassword');
    Route::match(['put', 'patch'], '/refresh-token', 'UserController@refreshToken');
    Route::patch('/validate-otp', 'UserController@validateOtp');
    Route::post('/request-otp', 'UserController@requestOtp');
    Route::delete('/logout', 'UserController@logout');
    Route::post('/login', 'UserController@login');
    Route::post('/register', 'UserController@register');
    Route::get('/version/{os}/{version}', 'VersionController@show');
    Route::get('/cache', 'SiteController@cache');
    Route::get('/health', 'SiteController@health');
});

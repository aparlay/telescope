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


Route::prefix('v1')->group(function () {
    Route::prefix('media')->group(function () {
        Route::match(['head', 'get'], '/', 'MediaController@index');
        Route::match(['head', 'get'], '/{media}', 'MediaController@show');
        Route::match(['get', 'post'], '/upload', 'MediaController@upload');
        Route::post('/', 'MediaController@store');
        Route::delete('/{media}', 'MediaController@destroy');
        Route::match(['put', 'patch'], '/{media}', 'MediaController@update');

        Route::put('/{media}/like', 'MediaLikeController@store');
        Route::delete('/{media}/like', 'MediaLikeController@destroy');

        Route::post('/{media}/report', 'ReportController@media');
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

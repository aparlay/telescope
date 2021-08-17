<?php

use Aparlay\Core\Api\V1\Controllers\AlertController;
use Aparlay\Core\Api\V1\Controllers\AuthController;
use Aparlay\Core\Api\V1\Controllers\BlockController;
use Aparlay\Core\Api\V1\Controllers\FollowController;
use Aparlay\Core\Api\V1\Controllers\MediaController;
use Aparlay\Core\Api\V1\Controllers\MediaLikeController;
use Aparlay\Core\Api\V1\Controllers\ReportController;
use Aparlay\Core\Api\V1\Controllers\SiteController;
use Aparlay\Core\Api\V1\Controllers\UserController;
use Aparlay\Core\Api\V1\Controllers\VersionController;
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
Route::middleware(['api', 'format-response'])->name('core.api.v1.')->prefix('v1')->group(function () {
    Route::prefix('media')->name('media.')->group(function () {
        Route::match(['head', 'get'], '/', [MediaController::class, 'index'])->name('list');
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('delete');
        Route::match(['put', 'patch'], '/{media}', [MediaController::class, 'update'])->name('update');
        Route::match(['head', 'get'], '/{media}', [MediaController::class, 'show'])
            ->middleware('cache.headers:public;max_age=2628000;etag')->name('show');
        Route::post('/{media}/report', [ReportController::class, 'media'])->name('report');
        Route::middleware('auth:api')->group(function () {
            Route::post('/', [MediaController::class, 'store'])->name('create');
            Route::match(['get', 'post'], '/upload', [MediaController::class, 'upload'])->name('upload');
            Route::middleware('auth:api')->put('/{media}/like', [MediaLikeController::class, 'store'])->name('like');
            Route::middleware('auth:api')->delete('/{media}/like', [MediaLikeController::class, 'destroy'])->name('unlike');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/{type}', [UserController::class, 'index'])
            ->where(['type' => '(likes|blocks|followers|followings|hashtags)'])->name('list');

        Route::post('/{user}/report', [ReportController::class, 'user'])->name('report');

        Route::get('/{user}/media', [MediaController::class, 'listByUser'])->name('media_list');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');

        Route::middleware('auth:api')->group(function () {
            Route::put('/{user}/block', [BlockController::class, 'store'])->name('block');
            Route::delete('/{user}/block', [BlockController::class, 'destroy'])->name('unblock');

            Route::put('/{user}/follow', [FollowController::class, 'store'])->name('follow');
            Route::delete('/{user}/follow', [FollowController::class, 'destroy'])->name('unfollow');
        });
    });

    Route::middleware('auth:api')->prefix('me')->name('profie.')->group(function () {
        Route::get('/', [UserController::class, 'me']);
        Route::match(['put', 'patch'], '/', [UserController::class, 'update']);
        Route::delete('/', [UserController::class, 'destroy']);
        Route::get('/token', [UserController::class, 'token']);
    });

    Route::middleware('auth:api')->match(['put', 'patch'], '/{alert}', [AlertController::class, 'update'])->name('alert.update');
    Route::match(['put', 'patch'], '/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
    Route::patch('/validate-otp', [UserController::class, 'validateOtp'])->name('user.validateOtp');
    Route::post('/request-otp', [UserController::class, 'requestOtp'])->name('user.requestOtp');
    Route::middleware('auth:api')->delete('/logout', [UserController::class, 'logout'])->name('user.logout');

    Route::post('/login', [AuthController::class, 'login'])->name('user.login');
    Route::post('/register', [AuthController::class, 'register'])->name('user.register');
    Route::match(['put', 'patch'], '/refresh', [AuthController::class, 'refresh'])->name('user.refreshToken');

    Route::get('/version/{os}/{version}', [VersionController::class, 'show'])->name('version.show');
    Route::get('/cache', [SiteController::class, 'cache'])->name('site.cache');
    Route::get('/health', [SiteController::class, 'health'])->name('site.health');
});

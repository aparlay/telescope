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
Route::middleware(['api', 'format-response', 'device-id', 'device-id-trottle:60'])->name('core.api.v1.')->prefix('v1')->group(function () {

    /* Media Prefix Group */
    Route::prefix('media')->name('media.')->group(function () {
        Route::match(['put', 'patch'], '/{media}', [MediaController::class, 'update'])->name('update');
        Route::post('/{media}/report', [ReportController::class, 'media'])->name('report');

        /* Authentication Group */
        Route::middleware(['auth:api', 'cookies-auth'])->group(function () {
            Route::match(['head', 'get'], '/', [MediaController::class, 'index'])->name('list')->withoutMiddleware(['auth:api']);
            Route::post('/', [MediaController::class, 'store'])->name('create');
            Route::match(['get', 'post'], '/upload', [MediaController::class, 'upload'])->name('upload');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('delete');
            Route::put('/{media}/like', [MediaLikeController::class, 'store'])->name('like');
            Route::delete('/{media}/like', [MediaLikeController::class, 'destroy'])->name('unlike');
        });

        /* Cache Group */
        Route::middleware(['cache.headers:public;max_age=2628000;etag', 'auth:api', 'cookies-auth'])->group(function () {
            Route::get('/{media}', [MediaController::class, 'show'])->name('show')->withoutMiddleware(['auth:api']);
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/{type}', [UserController::class, 'index'])
            ->where(['type' => '(likes|blocks|followers|followings|hashtags)'])->name('list');

        Route::post('/{user}/report', [ReportController::class, 'user'])->name('report');
        Route::get('/{user}/media', [MediaController::class, 'listByUser'])->name('media.list');

        /* Authentication Group with user prifix */
        Route::middleware(['auth:api', 'cookies-auth'])->group(function () {
            Route::put('/{user}/block', [BlockController::class, 'store'])->name('block');
            Route::delete('/{user}/block', [BlockController::class, 'destroy'])->name('unblock');
            Route::put('/{user}/follow', [FollowController::class, 'store'])->name('follow');
            Route::delete('/{user}/follow', [FollowController::class, 'destroy'])->name('unfollow');
            Route::get('/{user}', [UserController::class, 'show'])->name('show')->withoutMiddleware(['auth:api']);
        });
    });

    /* Authentication Group with me prefix */
    Route::middleware(['auth:api', 'cookies-auth'])->name('profie.')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('user.me');
            Route::delete('/', [UserController::class, 'destroy']);
            Route::match(['put', 'patch'], '/', [UserController::class, 'update']);
            Route::get('/token', [UserController::class, 'token']);
        });
    });

    /* Authentication Group */
    Route::middleware(['auth:api', 'cookies-auth'])->group(function () {
        Route::match(['put', 'patch'], '/alert/{alert}', [AlertController::class, 'update'])
            ->name('alert.update');
    });

    /* OTP Endpoints */
    Route::match(['put', 'patch'], '/change-password', [AuthController::class, 'changePassword'])
        ->name('user.change-password');
    Route::patch('/validate-otp', [AuthController::class, 'validateOtp'])->name('user.validateOtp');
    Route::post('/request-otp', [AuthController::class, 'requestOtp'])->name('user.requestOtp');

    Route::post('/login', [AuthController::class, 'login'])->name('user.login');
    Route::post('/register', [AuthController::class, 'register'])->name('user.register');
    Route::match(['put', 'patch'], '/refresh', [AuthController::class, 'refresh'])
        ->name('user.refreshToken');

    /* Without Middleware Endpoints */
    Route::get('/version/{os}/{version}', [VersionController::class, 'show'])
        ->name('version.show')
        ->withoutMiddleware(['device-id']);
    Route::get('/cache', [SiteController::class, 'cache'])
        ->name('site.cache')
        ->withoutMiddleware(['device-id']);
    Route::get('/health', [SiteController::class, 'health'])
        ->name('site.health')
        ->withoutMiddleware(['device-id']);
});

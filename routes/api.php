<?php

use Aparlay\Core\Api\V1\Controllers\AlertController;
use Aparlay\Core\Api\V1\Controllers\AuthController;
use Aparlay\Core\Api\V1\Controllers\BlockController;
use Aparlay\Core\Api\V1\Controllers\ContactUsController;
use Aparlay\Core\Api\V1\Controllers\FollowController;
use Aparlay\Core\Api\V1\Controllers\MediaCommentController;
use Aparlay\Core\Api\V1\Controllers\MediaController;
use Aparlay\Core\Api\V1\Controllers\MediaLikeController;
use Aparlay\Core\Api\V1\Controllers\ReportController;
use Aparlay\Core\Api\V1\Controllers\SiteController;
use Aparlay\Core\Api\V1\Controllers\UserController;
use Aparlay\Core\Api\V1\Controllers\UserDocumentController;
use Aparlay\Core\Api\V1\Controllers\UserNotificationController;
use Aparlay\Core\Api\V1\Controllers\VersionController;
use Aparlay\Core\Api\V1\Controllers\WebhookController;
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
Route::middleware(['api', 'format-response', 'device-id', 'device-id-throttle'])->name('core.api.v1.')->prefix('v1')->group(function () {
    /* Media Prefix Group */
    Route::prefix('media')->name('media.')->group(function () {
        Route::match(['put', 'patch'], '/{media}', [MediaController::class, 'update'])->name('update');
        Route::post('/{media}/report', [ReportController::class, 'media'])->name('report');

        /* Authentication Group */
        Route::middleware(['auth:api', 'cookies-auth'])->group(function () {
            Route::post('/', [MediaController::class, 'store'])->name('create');
            Route::match(['get', 'post'], '/upload', [MediaController::class, 'splitUpload'])->name('chunkUpload');
            Route::match(['get', 'post'], '/upload/split', [MediaController::class, 'splitUpload'])->name('splitUpload');
            Route::post('/upload/stream', [MediaController::class, 'streamUpload'])->name('streamUpload');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('delete');
            Route::put('/{media}/like', [MediaLikeController::class, 'store'])->name('like');
            Route::delete('/{media}/like', [MediaLikeController::class, 'destroy'])->name('unlike');

            Route::post('/{media}/comment', [MediaCommentController::class, 'store'])->name('comment.create');
            Route::post('/{media}/comment/{mediaComment}/reply', [MediaCommentController::class, 'reply'])->name('comment.reply.create');
            Route::get('/{media}/comment/{mediaComment}/reply', [MediaCommentController::class, 'listReplies'])->name('comment.reply.list');
            Route::delete('/{media}/comment/{mediaComment}', [MediaCommentController::class, 'destroy'])->name('comment.delete');
            Route::get('/{media}/comment', [MediaCommentController::class, 'list'])->name('comment.list');

            Route::match(['put', 'patch'], '/{media}/comment/{mediaComment}/like', [MediaCommentController::class, 'like'])->name('comment.like');
            Route::delete('/{media}/comment/{mediaComment}/like', [MediaCommentController::class, 'unlike'])->name('comment.unlike');

            Route::post('/{media}/comment/{mediaComment}/report', [ReportController::class, 'comment'])->name('comment.report');
        });

        /* Optional Auth Group */
        Route::middleware(['cookies-auth', 'optional-auth'])->group(function () {
            Route::match(['head', 'get'], '/', [MediaController::class, 'index'])->name('list');
            Route::match(['head', 'get'], '/{media}', [MediaController::class, 'show'])->name('show');
        });
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/{type}', [UserController::class, 'index'])
            ->where(['type' => '(likes|blocks|followers|followings|hashtags)'])->name('list');

        Route::post('/{user}/report', [ReportController::class, 'user'])->name('report');
        Route::middleware(['cookies-auth', 'optional-auth'])
            ->match(['head', 'get'], '/{user}/media', [MediaController::class, 'listByUser'])
            ->name('media.list');

        /* Authentication Group with user prifix */
        Route::middleware(['auth:api', 'cookies-auth'])->group(function () {
            Route::put('/{user}/block', [BlockController::class, 'store'])->name('block');
            Route::delete('/{user}/block', [BlockController::class, 'destroy'])->name('unblock');
            Route::put('/{user}/follow', [FollowController::class, 'store'])->name('follow');
            Route::delete('/{user}/follow', [FollowController::class, 'destroy'])->name('unfollow');
            Route::get('/{user}', [UserController::class, 'show'])->name('show')->withoutMiddleware(['auth:api']);
        });
    });

    Route::middleware(['auth:api', 'cookies-auth'])
        ->prefix('user-document')
        ->name('user-document.')
        ->controller(UserDocumentController::class)->group(function () {
            /* Authentication Group with user prefix */
            Route::post('/', 'store')->name('store');
            Route::get('/', 'index')->name('index');
            Route::put('/send-to-verification', 'sendToVerification')->name('send-to-verification');
            Route::get('/{userDocument}', 'view')->name('view');
        });

    Route::middleware(['auth:api', 'cookies-auth'])
        ->prefix('user-notification')
        ->name('user-notification.')
        ->controller(UserNotificationController::class)->group(function () {
            /* Authentication Group with user prefix */
            Route::get('/', 'index')->name('index');
            Route::put('/read', 'read')->name('read');
        });

    /* Authentication Group with me prefix */
    Route::middleware(['auth:api', 'cookies-auth'])->name('profile.')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::prefix('me')->controller(UserController::class)->group(function () {
            Route::get('/', 'me')->name('user.me');
            Route::post('/delete', 'destroy');
            Route::match(['put', 'patch'], '/', 'update');
            Route::get('/token', 'token');
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

    Route::post('/contact-us', [ContactUsController::class, 'send'])
        ->name('contactus')
        ->withoutMiddleware(['device-id']);
});

Route::get('/metrics', [SiteController::class, 'metrics'])
    ->name('site.metrics')
    ->withoutMiddleware(['device-id']);

Route::post('/v1/webhook/pusher', [WebhookController::class, 'pusher'])->name('webhook-pusher');

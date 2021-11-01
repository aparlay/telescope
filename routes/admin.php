<?php

use Aparlay\Core\Admin\Controllers\AlertController;
use Aparlay\Core\Admin\Controllers\AuthController;
use Aparlay\Core\Admin\Controllers\DashboardController;
use Aparlay\Core\Admin\Controllers\MediaController;
use Aparlay\Core\Admin\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::domain(config('core.admin.domain'))->middleware(['admin'])->name('core.admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('core.admin.dashboard');
    });

    /* Authenticated Routes */
    Route::middleware(['admin-auth:admin', 'role:support|administrator|super-administrator'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])
            ->middleware(['permission:dashboard'])
            ->name('dashboard');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware(['admin-auth:admin'])->name('alert.')->group(function () {
            Route::post('/alert/create', [AlertController::class, 'create'])
            ->middleware(['permission:create alerts'])
            ->name('alert.create');
        });

        /* Media routes */
        Route::middleware(['admin-auth:admin'])->name('media.')->group(function () {
            Route::get('media', [MediaController::class, 'index'])
                ->middleware(['permission:list medias'])
                ->name('index');
            Route::get('media/moderation', [MediaController::class, 'moderation'])
                ->middleware(['permission:list medias'])
                ->name('moderation');
            Route::get('media/{id}', [MediaController::class, 'view'])
                ->middleware(['permission:show medias'])
                ->name('view');
            Route::post('media/{id}', [MediaController::class, 'update'])
                ->middleware(['permission:edit medias'])
                ->name('update');
            Route::post('reprocess/{id}', [MediaController::class, 'reprocess'])
                ->middleware(['permission:edit medias'])
                ->name('reprocess');
            Route::get('download-original/{id}/{hash}', [MediaController::class, 'downloadOriginal'])
                ->middleware(['permission:edit medias'])
                ->name('downloadOriginal');
            Route::get('pending/{id}/{order}', [MediaController::class, 'pending'])
                ->middleware(['permission:show medias'])
                ->name('pending');
        });

        /* User Routes */
        Route::name('user.')->group(function () {
            Route::get('user', [UserController::class, 'index'])
                ->middleware(['permission:list users'])
                ->name('index');
            Route::get('user/{user}', [UserController::class, 'view'])
                ->middleware(['permission:show users'])
                ->name('view');
            Route::put('user/{user}', [UserController::class, 'update'])
                ->middleware(['permission:edit users'])
                ->name('update');
            Route::match(['get', 'post'], 'user/upload-media', [UserController::class, 'uploadMedia'])
                    ->middleware(['permission:upload medias'])
                    ->name('media.upload');
            Route::patch('user/{user}', [UserController::class, 'updateStatus'])
                ->middleware(['permission:edit users'])
                ->name('update.status');
        });

        Route::name('alert.')->group(function () {
            Route::post('alert', [AlertController::class, 'store'])
               ->middleware('permission:create alerts')
               ->name('store');
        });

        /* Ajax Routes */
        Route::name('ajax.')->prefix('ajax')->group(function () {
            Route::get('user', [UserController::class, 'indexAjax'])
                ->middleware(['permission:list users'])
                ->name('user.index');
            Route::get('media', [MediaController::class, 'indexAjax'])
                ->middleware(['permission:list medias'])
                ->name('media.index');
        });
    });

    /* Login Routes */
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AuthController::class, 'viewLogin'])->name('login');
        Route::post('login', [AuthController::class, 'postLogin'])->name('login.post');
    });
});

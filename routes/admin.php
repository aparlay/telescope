<?php

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
    Route::middleware(['admin-auth:admin', 'role:support,administrator,super-administrator'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])
            ->middleware(['permission:dashboard'])
            ->name('dashboard');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');

        /* Media routes */
        Route::middleware(['admin-auth:admin'])->name('media.')->group(function () {
            Route::get('media', [MediaController::class, 'index'])
                ->middleware(['permission:list medias'])
                ->name('index');
            Route::get('media/{id}', [MediaController::class, 'view'])
                ->middleware(['permission:show medias'])
                ->name('view');
            Route::post('media/{id}', [MediaController::class, 'update'])
                ->middleware(['permission:edit medias'])
                ->name('update');
        });

        /* User Routes */
        Route::name('user.')->group(function () {
            Route::get('user', [UserController::class, 'index'])
                ->middleware(['permission:list users'])
                ->name('index');
            Route::get('user/{id}', [UserController::class, 'view'])
                ->middleware(['permission:show users'])
                ->name('view');
        });

        /* Ajax Routes */
        Route::name('ajax.')->prefix('ajax')->group(function () {
            Route::get('user', [UserController::class, 'indexAjax'])
                ->middleware(['permission:list users'])
                ->name('user.index');
        });
    });

    /* Login Routes */
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AuthController::class, 'viewLogin'])->name('login');
        Route::post('login', [AuthController::class, 'postLogin'])->name('login.post');
    });
});

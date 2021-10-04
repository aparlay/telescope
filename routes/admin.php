<?php

use Aparlay\Core\Admin\Controllers\AuthController;
use Aparlay\Core\Admin\Controllers\DashboardController;
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

Route::domain(config('core.admin.url'))->middleware(['admin'])->name('core.admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('core.admin.dashboard');
    });

    /*Authenticated Routes */
    Route::middleware(['admin-auth:admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    });

    /* User Routes */
    Route::middleware(['admin-auth:admin'])->name('user.')->group(function () {
        Route::get('user', [UserController::class, 'index'])->name('index');
    });

    /* Login Routes */
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AuthController::class, 'viewLogin'])->name('login');
        Route::post('login', [AuthController::class, 'postLogin'])->name('login.post');
    });
});

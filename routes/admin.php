<?php

use Aparlay\Core\Admin\Controllers\AlertController;
use Aparlay\Core\Admin\Controllers\AuthController;
use Aparlay\Core\Admin\Controllers\DashboardController;
use Aparlay\Core\Admin\Controllers\EmailController;
use Aparlay\Core\Admin\Controllers\MediaController;
use Aparlay\Core\Admin\Controllers\NoteController;
use Aparlay\Core\Admin\Controllers\RoleController;
use Aparlay\Core\Admin\Controllers\SettingController;
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
                ->middleware(['permission:list medias-moderation'])
                ->name('moderation');

            Route::get('media/algorithms', [MediaController::class, 'algorithms'])
                ->middleware(['permission:list medias-algorithms'])
                ->name('algorithms');

            Route::get('media/moderation-queue', [MediaController::class, 'moderationQueue'])
                ->middleware(['permission:queue medias-moderation'])
                ->name('moderation-queue');

            Route::get('media/moderation-queue/next/{mediaId}&direction={direction}', [MediaController::class, 'moderationNextOrPrev'])
                ->middleware(['permission:queue medias-moderation'])
                ->name('moderation-queue.next');

            Route::get('media/{media}', [MediaController::class, 'view'])
                ->middleware(['permission:show medias'])
                ->name('view');
            Route::post('media/{media}', [MediaController::class, 'update'])
                ->middleware(['permission:edit medias'])
                ->name('update');
            Route::post('media/{media}/score', [MediaController::class, 'updateScore'])
                ->middleware(['permission:edit medias'])
                ->name('updateScore');
            Route::post('reprocess/{media}', [MediaController::class, 'reprocess'])
                ->middleware(['permission:edit medias'])
                ->name('reprocess');
            Route::get('download-original/{media}/{hash}', [MediaController::class, 'downloadOriginal'])
                ->middleware(['permission:edit medias'])
                ->name('downloadOriginal');
            Route::get('recalculate-sort-score/{media}', [MediaController::class, 'recalculateSortScore'])
                ->middleware(['permission:edit medias'])
                ->name('recalculateSortScore');
            Route::get('pending/media/{page}', [MediaController::class, 'pending'])
                ->middleware(['permission:show medias'])
                ->name('pending');
            Route::post('media/{media}/reupload', [MediaController::class, 'reupload'])
                ->middleware(['permission:upload medias'])
                ->name('reupload');
        });

        /* User Routes */
        Route::name('user.')->group(function () {
            Route::get('user', [UserController::class, 'index'])
                ->middleware(['permission:list users'])
                ->name('index');

            Route::get('user/moderation', [UserController::class, 'moderation'])
                ->middleware(['permission:list users-moderation'])
                ->name('moderation');

            Route::get('user/moderation-queue/next/{userId?}&direction={direction}', [UserController::class, 'moderationNextOrPrev'])
                ->middleware(['permission:queue users-moderation'])
                ->name('moderation-queue.next');

            Route::get('user/moderation-queue', [UserController::class, 'moderationQueue'])
                ->middleware(['permission:queue users-moderation'])
                ->name('moderation-queue');

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
            Route::post('user/media/upload', [UserController::class, 'upload'])
                ->middleware(['permission:upload medias'])
                ->name('media.save-upload');
            Route::get('user/login/{user}', [UserController::class, 'loginAsUser'])
                ->middleware(['permission:edit users'])
                ->name('login_as_user');
        });

        Route::name('alert.')->group(function () {
            Route::post('alert', [AlertController::class, 'store'])
               ->middleware('permission:create alerts')
               ->name('store');

            Route::post('alert/{user}', [AlertController::class, 'storeForUSer'])
                ->middleware('permission:create alerts')
                ->name('store.user');
        });

        /* E-mail Route */
        Route::get('email', [EmailController::class, 'index'])
            ->middleware(['permission:list emails'])
            ->name('email.index');

        /* Ajax Routes */
        Route::name('ajax.')->prefix('ajax')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'indexAjax'])
                ->middleware(['permission:dashboard'])
                ->name('dashboard.index');
        });

        Route::name('role.')->group(function () {
            Route::get('role', [RoleController::class, 'index'])
                ->middleware(['permission:list roles'])
                ->name('index');

            Route::post('role/{role}/', [RoleController::class, 'updateRole'])
                ->middleware(['permission:edit roles'])
                ->name('update');
        });

        Route::name('setting.')->group(function () {
            Route::get('setting', [SettingController::class, 'index'])
                ->middleware(['permission:list settings'])
                ->name('index');

            Route::get('setting/create', [SettingController::class, 'create'])
                ->middleware(['permission:create settings'])
                ->name('create');

            Route::post('setting/store', [SettingController::class, 'store'])
                ->middleware(['permission:create settings'])
                ->name('store');

            Route::get('setting/{setting}', [SettingController::class, 'view'])
                ->middleware(['permission:show settings'])
                ->name('view');

            Route::put('setting/{setting}', [SettingController::class, 'update'])
                ->middleware(['permission:edit settings'])
                ->name('update');

            Route::delete('setting/{setting}', [SettingController::class, 'delete'])
                ->middleware(['permission:delete settings'])
                ->name('delete');
        });

        Route::name('note.')->group(function () {
            Route::post('note/{user}', [NoteController::class, 'store'])
            ->middleware(['permission:create notes'])
            ->name('store');
        });
    });

    /* Login Routes */
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AuthController::class, 'viewLogin'])->name('login');
        Route::post('login', [AuthController::class, 'postLogin'])->name('login.post');
    });
});

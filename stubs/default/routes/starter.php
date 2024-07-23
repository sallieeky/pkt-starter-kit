<?php

use App\Http\Controllers\Starter\AccountController;
use App\Http\Controllers\Starter\GlobalSearchController;
use App\Http\Controllers\Starter\NotificationController;
use App\Http\Controllers\Starter\UserLogController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::authenticated()->group(function () {
    Route::controller(NotificationController::class)->group(function() {
        Route::get('/notification','notificationPage')->name('notification.browse');
        Route::get('/notification/data','notificationPagination')->name('notification.data');
        Route::post('/notifications/read', 'markAsRead')->name('notification.mark_as_read');
    });

    Route::controller(AccountController::class)->group(function(){
        Route::get('/account','accountPage')->name('account');
        Route::get('/picture/{npk}', 'accountPicture')->name('account-picture');
    });

    Route::controller(UserLogController::class)->group(function () {
        Route::get('/user-log', 'userLogPage')->name('user_log.browse')->can('user_log.browse');
        Route::get('/user-log/{filename}', 'getLogFileDetail')->name('user_log.detail')->can('user_log.browse');
    });

    Route::get('/global-search', GlobalSearchController::class)->name('global.search');

    if (class_exists(\App\Models\Media::class) && class_exists(\App\Http\Controllers\Starter\MediaController::class)) {
        Route::controller(\App\Http\Controllers\Starter\MediaController::class)->group(function(){
            Route::get('/get-media/{media:uuid}', 'getMedia')->name('get-media');
            Route::get('/upload-media/{collection}', 'uploadMedia')->name('upload-media');
        });
    }
});
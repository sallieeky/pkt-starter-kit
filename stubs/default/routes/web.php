<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/login', [AuthenticationController::class, 'loginPage'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'login'])->name('login.attempt')->middleware('guest');

Route::middleware(config('sso-session.ENABLE_SSO') ? ['SsoPortal'] : ['auth'])->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('home'));
    Route::get('/dashboard', fn () => Inertia::render('Home', []))->name('home');

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'userManagePage')->name('user.browse')->can('user.browse');
        Route::get('/user/data-processing', 'dataprocessing')->name('user.data_processing')->can('user.browse');
        Route::post('/user', 'create')->name('user.create')->can('user.create');
        Route::put('/user/{user:user_uuid}', 'update')->name('user.update')->can('user.update');
        Route::delete('/user/{user:user_uuid}', 'delete')->name('user.delete')->can('user.delete');
        Route::put('/user/{user:user_uuid}/switch-status', 'switchStatus')->name('user.switch_status')->can('user.update');
        Route::post('/user/sync-leader', 'syncLeader')->name('user.sync_leader')->can('user.update');
    });
    Route::controller(RoleAndPermissionController::class)->group(function () {
        Route::get('/role-and-permission', 'roleAndPemissionManagePage')->name('role.browse')->can('role.browse');
        Route::post('/role', 'create')->name('role.create')->can('role.create');
        Route::put('/role/{role}', 'update')->name('role.update')->can('role.update');
        Route::delete('/role/{role}', 'delete')->name('role.delete')->can('role.delete');
        Route::get('/role/{role}/permissions', 'getRolePermission')->name('role.permission_list')->can('role.browse');
        Route::get('/role/{role}/users', 'getRoleUser')->name('role.user_list')->can('role.browse');
        Route::put('/role/{role}/switch-permission', 'switchPermission')->name('role.switch_permission')->can('role.assign_permission');
    });
});

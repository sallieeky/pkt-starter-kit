<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('sso-session')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\SsoSessionController::class, 'auth']);
    Route::post('/set', [\App\Http\Controllers\Api\SsoSessionController::class, 'set']);
    Route::post('/destroy', [\App\Http\Controllers\Api\SsoSessionController::class, 'destroy']);
    Route::post('/sync-users', [\App\Http\Controllers\Api\SsoSessionController::class, 'syncUsers']);
});
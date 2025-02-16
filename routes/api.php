<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::delete('auth', [AuthController::class, 'delete'])->name('auth.delete');
    Route::get('users/me', [UserController::class, "showMe"])->name('users.me');
    Route::apiResource('users', UserController::class)->except('store');
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('auth', [AuthController::class, 'store'])->name('auth.store');
});

Route::middleware('throttle:10,3600')->group(function () {
    Route::post('users', [UserController::class, 'store'])->name('users.store');
});

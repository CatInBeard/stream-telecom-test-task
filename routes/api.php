<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShortLinkController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::delete('auth', [AuthController::class, 'delete'])->name('auth.delete');
    Route::get('users/me', [UserController::class, "showMe"])->name('users.me');
    Route::apiResource('users', UserController::class)->except('store', 'show');
    Route::apiResource('short-links', ShortLinkController::class)->except('show');
    Route::post('short-links', [ShortLinkController::class, 'store'])->name('short-links.store')->middleware('throttle:5,1');
});

Route::get('short-links/{short_link}', [ShortLinkController::class, 'show'])->name('short-links.show');
Route::middleware('throttle:10,1')->group(function () {
    Route::post('auth', [AuthController::class, 'store'])->name('auth.store');
});

Route::middleware('throttle:10,3600')->group(function () {
    Route::post('users', [UserController::class, 'store'])->name('users.store');
});

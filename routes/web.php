<?php

use App\Http\Controllers\ShortLinkRedirectController;
use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('login');

Route::get('/{link}', [ShortLinkRedirectController::class, 'redirect'])->where('link', '[a-zA-Z0-9]+')->name('redirect-link.show');

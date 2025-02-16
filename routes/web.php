<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('login');

Route::get('/{link}', [ViewLinkController::class, 'show'])->where('link', '[a-zA-Z0-9]+')->name('view-link.show');

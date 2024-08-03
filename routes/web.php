<?php

use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('urls', [ShortUrlController::class, 'index'])
    ->middleware(['auth'])
    ->name('urls');

require __DIR__ . '/auth.php';

Route::get('/{key}', [ShortUrlController::class, 'go'])->name('go');

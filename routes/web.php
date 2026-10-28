<?php

use App\Http\Controllers\ChangelogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChangelogController::class, 'index'])->name('home');
Route::post('/', [ChangelogController::class, 'store'])->name('changelog.store');

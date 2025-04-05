<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('profile', [ProfileController::class, 'index'])->name('profile');

Route::post('/expenses/month-data', [ProfileController::class, 'getMonthData'])->name('expenses.monthData');

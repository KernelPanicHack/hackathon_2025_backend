<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController\LoginController;
use App\Http\Controllers\AuthController\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.send-form');
Route::get('logout', [LoginController::class, 'logout'])->name('login.logout');

Route::resource('register', RegisterController::class)->only(['store', 'create']);
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');

    Route::post('/expenses/month-data', [ProfileController::class, 'getMonthData'])->name('expenses.monthData');
});

<?php

use App\Http\Controllers\AuthController\LoginController;
use App\Http\Controllers\AuthController\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.send-form');
Route::get('logout', [LoginController::class, 'logout'])->name('login.logout');

Route::resource('register', RegisterController::class)->only(['store', 'create']);

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('index');
});

<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController\LoginController;
use App\Http\Controllers\AuthController\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.send-form');
Route::get('logout', [LoginController::class, 'logout'])->name('login.logout');

Route::resource('register', RegisterController::class)->only(['store', 'create']);
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/products/{id}', [ProductsController::class, 'index'])->name('products.index');
Route::post('/operations/{operationId}/change-category', [ProductsController::class, 'changeCategory'])->name('operations.change-category');
Route::patch('/item/{itemId}/update-category', [ProductsController::class, 'updateItemCategory'])->name('item.updateCategory');

Route::post('/operations/{id}/change-category', [ProductsController::class, 'changeCategory']);


Route::middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');

    Route::post('/expenses/month-data', [ProfileController::class, 'monthData'])->name('expenses.monthData');
});

<?php

use App\Http\Controllers\Api\WSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);
Route::get('/ws/getToken', [WSController::class, 'getToken']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

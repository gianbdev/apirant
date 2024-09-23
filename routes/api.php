<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::post('/register', [ApiController::class, 'register'])->name('register');
    Route::post('/login', [ApiController::class, 'login'])->name('login');
    Route::post('/logout', [ApiController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [ApiController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    //Route::post('/profile', [ApiController::class, 'profile'])->middleware('auth:api')->name('profile');
    Route::post('/profile', [ApiController::class, 'profile']);
});
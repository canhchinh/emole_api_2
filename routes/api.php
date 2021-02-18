<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserImageController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**************** user ****************/
Route::group(['prefix' => 'user'], function() {
    Route::post('register-step1', [UserController::class, 'registerStep1']);
    Route::post('login', [UserController::class, 'login']);
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::get('/reset-password', [UserController::class, 'resetPassword']);

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function() {
    Route::post('register-step2', [UserController::class, 'registerStep2']);
    Route::post('register-step3', [UserController::class, 'registerStep3']);
    Route::post('/reset-password', [UserController::class, 'newPassword']);
});

/**************** user image upload ****************/
Route::group(['prefix' => 'user_image', 'middleware' => 'auth:sanctum'], function() {
    Route::post('', [UserImageController::class, 'newImageUpload']);
});


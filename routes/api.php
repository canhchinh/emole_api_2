<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SnsController;
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
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function() {
    Route::get('', [UserController::class, 'userInfo']);
    Route::post('register-step2', [UserController::class, 'registerStep2']);
    Route::post('register-step3', [UserController::class, 'registerStep3']);
    Route::post('new-password', [UserController::class, 'newPassword']);
    Route::post('self-introduction', [UserController::class, 'updateSelfIntroduction']);
    Route::post('avatar', [UserController::class, 'avatar']);
    Route::group(['prefix' => 'image'], function() {
        Route::post('', [UserImageController::class, 'imageUpload']);
    });
});


/**************** career ****************/
Route::group(['prefix' => 'career', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list', [CareerController::class, 'listCareer']);
    Route::post('save', [CareerController::class, 'save']);
    Route::group(['prefix' => '{career_id}'], function() {
        Route::group(['prefix' => 'category'], function() {
            Route::get('list', [CategoryController::class, 'listCategory']);
        });
        Route::group(['prefix' => 'job'], function() {
            Route::get('list', [JobController::class, 'listJob']);
        });
        Route::group(['prefix' => 'genre'], function() {
            Route::get('list', [GenreController::class, 'listGenre']);
        });
    });
    Route::group(['prefix' => 'activity'], function() {
        Route::post('', [UserController::class, 'activity']);
    });
});
Route::group(['prefix' => 'education', 'middleware' => 'auth:sanctum'], function() {
    Route::post('', [UserController::class, 'education']);
});

/**************** sns ****************/
Route::group(['prefix' => 'sns', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list', [SnsController::class, 'listSns']);
    Route::post('save', [SnsController::class, 'save']);
});
Route::group(['prefix' => 'portfolio', 'middleware' => 'auth:sanctum'], function() {
    Route::post('', [UserController::class, 'portfolio']);
    Route::post('image', [UserController::class, 'portfolioImage']);
});

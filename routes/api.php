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
Route::get('/reset-password', [UserController::class, 'resetPassword']);

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function() {
    Route::post('register-step2', [UserController::class, 'registerStep2']);
    Route::post('register-step3', [UserController::class, 'registerStep3']);
    Route::post('reset-password', [UserController::class, 'newPassword']);
    Route::put('self-introduction', [UserController::class, 'updateSelfIntroduction']);
});

/**************** user image upload ****************/
Route::group(['prefix' => 'user_image', 'middleware' => 'auth:sanctum'], function() {
    Route::post('', [UserImageController::class, 'newImageUpload']);
});
/**************** career ****************/
Route::group(['prefix' => 'career', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list', [CareerController::class, 'listCareer']);
    /**************** category ****************/
    Route::group(['prefix' => '{career_id}/category', 'middleware' => 'auth:sanctum'], function() {
        Route::get('list', [CategoryController::class, 'listCategory']);
    });
    Route::post('user-select', [CareerController::class, 'careerUserSelect']);
});
/**************** job ****************/
Route::group(['prefix' => 'job', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list/{career_id}', [JobController::class, 'listJob']);
});
/**************** sns ****************/
Route::group(['prefix' => 'sns', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list', [SnsController::class, 'listSns']);
});
/**************** genre ****************/
Route::group(['prefix' => 'genre', 'middleware' => 'auth:sanctum'], function() {
    Route::get('list/{career_id}', [GenreController::class, 'listGenre']);
});
/**************** activity ****************/
Route::group(['prefix' => 'activity', 'middleware' => 'auth:sanctum'], function() {
    Route::post('', [UserController::class, 'activity']);
});



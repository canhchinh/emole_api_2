<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserImageController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SnsController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LineNotifyController;
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
Route::group(['prefix' => 'user'], function () {
    Route::post('register-step1', [UserController::class, 'registerStep1']);
    Route::post('login', [UserController::class, 'login']);
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::group(['prefix' => 'user', 'middleware' => 'auth:sanctum'], function () {
    Route::get('', [UserController::class, 'userInfo']);
    Route::post('register-step2', [UserController::class, 'registerStep2']);
    Route::post('register-step3', [UserController::class, 'registerStep3']);
    Route::post('new-password', [UserController::class, 'newPassword']);
    Route::post('self-introduction', [UserController::class, 'updateSelfIntroduction']);
    Route::post('follow', [UserController::class, 'postFollow']);
    Route::get('follow', [UserController::class, 'getFollow']);
    Route::get('follower', [UserController::class, 'getFollower']);

    Route::put('avatar', [UserController::class, 'avatar']);
    Route::group(['prefix' => 'image'], function () {
        Route::post('', [UserImageController::class, 'imageUpload']);
        Route::get('', [UserImageController::class, 'listImage']);
        Route::get('/{idUser}', [UserImageController::class, 'listImageByIdUser']);
    });
    Route::put('account-name', [UserController::class, 'accountName']);
    Route::put('basic-information', [UserController::class, 'basicInformation']);
    Route::put('email', [UserController::class, 'email']);
    Route::put('email-notification', [UserController::class, 'emailNotification']);
    Route::put('change-password', [UserController::class, 'changePassword']);
    Route::get('search', [UserController::class, 'searchUser']);
});

Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
    Route::get('list', [UserController::class, 'listUsers']);
});


/**************** career ****************/
Route::group(['prefix' => 'career', 'middleware' => 'auth:sanctum'], function () {
    Route::get('list', [CareerController::class, 'listCareer']);
    Route::get('/detail/{id}', [CareerController::class, 'detailForUser'])->where('id', '[0-9]+');

    Route::post('save', [CareerController::class, 'save']);
    Route::group(['prefix' => '{career_id}'], function () {
        Route::group(['prefix' => 'category'], function () {
            Route::get('list', [CategoryController::class, 'listCategory']);
        });
        Route::group(['prefix' => 'job'], function () {
            Route::get('list', [JobController::class, 'listJob']);
        });
        Route::group(['prefix' => 'genre'], function () {
            Route::get('list', [GenreController::class, 'listGenre']);
        });
        Route::get('info', [ActivityController::class, 'info']);
    });
    Route::group(['prefix' => 'activity'], function () {
        Route::post('', [UserController::class, 'activity']);
    });
});
Route::group(['prefix' => 'education', 'middleware' => 'auth:sanctum'], function () {
    Route::post('', [UserController::class, 'education']);
    Route::get('', [UserController::class, 'listWorkEducation']);
});

Route::group(['prefix' => 'sns', 'middleware' => 'auth:sanctum'], function () {
    Route::get('list', [SnsController::class, 'listSns']);
    Route::post('save', [SnsController::class, 'save']);
});
Route::group(['prefix' => 'portfolio', 'middleware' => 'auth:sanctum'], function () {
    Route::post('', [UserController::class, 'portfolio']);
    Route::get('detail/{portfolio_id}', [UserController::class, 'portfolioDetail']);
    Route::post('image', [UserController::class, 'portfolioImage']);
    Route::get('list', [UserController::class, 'ListPortfolio']);
});
Route::group(['prefix' => 'activity-base', 'middleware' => 'auth:sanctum'], function () {
    Route::get('', [ActivityController::class, 'listActivityBase']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user-career', [CareerController::class, 'userCareer']);
    Route::get('user-career/{idUser}', [CareerController::class, 'detailUserCareer']);
    Route::get('job-description', [CareerController::class, 'jobDescription']);
    Route::post('profile', [UserController::class, 'updateProfile']);
});

/**************** Line Notify ****************/
Route::group(['prefix' => 'line-notify', 'middleware' => 'auth:sanctum'], function() {
    Route::get('get-auth-link', [LineNotifyController::class, 'getAuthLink']);
});
Route::group(['prefix' => 'line-notify', 'middleware' => 'auth:sanctum'], function() {
    Route::get('get-access-token', [LineNotifyController::class, 'getAccessToken']);
});
Route::group(['prefix' => 'line-notify', 'middleware' => 'auth:sanctum'], function() {
    Route::post('send-notify', [LineNotifyController::class, 'sendNotify']);
});


<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('admin.auth.login');
Route::match(['get', 'post'], '/forgot-password', [AuthController::class, 'forgotPassword'])->name('admin.auth.forgot');
Route::get('/reset-password/{token}', [AuthController::class, 'getResetPassword'])->name('admin.auth.get.resetPassword');
Route::post('/reset-password', [AuthController::class, 'postResetPassword'])->name('admin.auth.post.newPassword');
Route::middleware('auth')->group(function () {
    Route::get('/log/view', [HomeController::class, 'logView'])->name('admin.home.logView'); // TODO: remove

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');
    Route::get('/', [HomeController::class, 'index'])->name('admin.home.index');
    Route::prefix('user')->group(function () {
        Route::get('/', [AdminUserController::class, 'listUser'])->name('admin.users.list');
        Route::get('/view/{id}', [AdminUserController::class, 'detailUser'])->name('admin.users.detail');
        Route::match(['put'], '/change/status/{id}', [AdminUserController::class, 'updateUserStatus'])->name('admin.user.update.status');
        Route::delete('/delete/{id}', [AdminUserController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/send-email-to-user', [AdminUserController::class, 'sendEmailToUser'])->name('admin.users.sendEmail');
        Route::post('/user/send-to-all-email', [AdminUserController::class, 'sendEmailToAllUser'])->name('admin.user.sendEmailAll');
    });

    Route::prefix('portfolio')->group(function () {
        Route::get('/', [AdminPortfolioController::class, 'listPortfolio'])->name('admin.portfolio.list');
        Route::delete('/delete/{id}', [AdminPortfolioController::class, 'deletePortfolio'])->name('admin.portfolio.delete');
        Route::match(['put'], '/change/status/{id}/{key}', [AdminPortfolioController::class, 'updatePortfolioStatus'])->name('admin.portfolio.update.status');
        Route::post('/send-email-to-portfolio', [AdminPortfolioController::class, 'sendEmailToPortfolio'])->name('admin.portfolio.sendEmail');
    });
    Route::prefix('notify')->group(function () {
        Route::get('/', [AdminNotificationController::class, 'listNotify'])->name('admin.notify.list');
        Route::match(['get', 'post'], '/create', [AdminNotificationController::class, 'createNotify'])->name('admin.notify.create');
        Route::match(['get', 'post'], '/view/{id}', [AdminNotificationController::class, 'viewNotify'])->name('admin.notify.view');
        Route::match(['delete'], '/delete/{id}', [AdminNotificationController::class, 'deleteNotify'])->name('admin.notify.delete');
        Route::match(['put'], '/change/status/{id}', [AdminNotificationController::class, 'updateNotifyStatus'])->name('admin.notify.update.status');
    });
    Route::get('/detail/{id}', [HomeController::class, 'detailUser'])->name('admin.users.detailUser');
});

Route::match(['get', 'post'], '/import', [HomeController::class, 'import'])->name('import');
Route::get('/test-image', [HomeController::class, 'testImage'])->name('testImage');
Route::get('/update-opg', [HomeController::class, 'updateOpg'])->name('updateOpg');
Route::get('/update-img', [HomeController::class, 'updateImg'])->name('updateImg');
Route::get('/update-career', [HomeController::class, 'updateCareer'])->name('updateCareer');
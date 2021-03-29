<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
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
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');
    Route::get('/', [HomeController::class, 'index'])->name('admin.home.index');
    Route::prefix('user')->group(function () {
        Route::get('/', [HomeController::class, 'listUser'])->name('admin.users.list');
    });
    Route::prefix('portfolio')->group(function () {
        Route::get('/', [HomeController::class, 'listPortfolio'])->name('admin.portfolio.list');
    });
    Route::prefix('notify')->group(function () {
        Route::get('/{status?}', [HomeController::class, 'listNotify'])->name('admin.notify.list');
        Route::match(['get', 'post'], '/create', [HomeController::class, 'createNotify'])->name('admin.notify.create');
        Route::match(['get', 'post'], '/view/{id}', [HomeController::class, 'createNotify'])->name('admin.notify.view');
        Route::match(['delete'], '/delete/{id}', [HomeController::class, 'deleteNotify'])->name('admin.notify.delete');
    });
    Route::get('/detail/{id}', [HomeController::class, 'detailUser'])->name('admin.users.detailUser');
});

Route::match(['get', 'post'],'/import', [HomeController::class, 'import'])->name('import');

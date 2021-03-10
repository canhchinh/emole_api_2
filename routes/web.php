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
    Route::get('/', [HomeController::class, 'index'])->name('admin.home.index');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.auth.logout');
});

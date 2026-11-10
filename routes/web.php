<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CancelTransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Tidak ada perubahan yang diperlukan di file ini.

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/kasir', [DashboardController::class, 'kasirDashboard'])->middleware(['auth', 'kasir'])->name('kasir.dashboard');
Route::get('/admin', [DashboardController::class, 'adminDashboard'])->middleware(['auth', 'admin'])->name('admin.dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], '/switch-user/{role?}', [AuthController::class, 'switchUser'])->name('user.switch');

Route::post('/transactions', [TransactionController::class, 'storeTransaction'])->name('transactions.store');

Route::post('/transactions/{id}/cancel', [CancelTransactionController::class, 'cancelTransaction'])->name('transactions.cancel');

Route::get('/check-connection', [UserManagementController::class, 'checkConnection'])->name('check.connection');
Route::post('/test-privilege/{action}', [UserManagementController::class, 'testPrivilege'])->name('test.privilege');

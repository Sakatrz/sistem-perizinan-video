<?php

use App\Http\Controllers\Admin\AccessRequestController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\VideoController as CustomerVideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin.videos.index')
        : redirect()->route('customer.videos.index');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('videos', AdminVideoController::class)->except('show');

    Route::get('requests', [AccessRequestController::class, 'index'])->name('requests.index');
    Route::post('requests/{accessRequest}/approve', [AccessRequestController::class, 'approve'])->name('requests.approve');
    Route::post('requests/{accessRequest}/update-access', [AccessRequestController::class, 'updateAccess'])->name('requests.update-access');
    Route::post('requests/{accessRequest}/revoke', [AccessRequestController::class, 'revoke'])->name('requests.revoke');
    Route::post('requests/{accessRequest}/reject', [AccessRequestController::class, 'reject'])->name('requests.reject');
});

// CUSTOMER
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('videos', [CustomerVideoController::class, 'index'])->name('videos.index');
    Route::post('videos/{video}/request', [CustomerVideoController::class, 'requestAccess'])->name('videos.request');
    Route::get('videos/{video}/watch', [CustomerVideoController::class, 'watch'])->name('videos.watch');
    Route::get('videos/{video}/stream', [CustomerVideoController::class, 'stream'])->name('videos.stream');
});

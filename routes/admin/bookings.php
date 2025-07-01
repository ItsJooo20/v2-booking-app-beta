<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/store', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/{booking}/update', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/{booking}/destroy', [BookingController::class, 'destroy'])->name('bookings.destroy');
        Route::post('/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        
        Route::get('/{booking}/return', [BookingController::class, 'showReturnForm'])->name('bookings.return.show');
        Route::post('/{booking}/return', [BookingController::class, 'submitReturn'])->name('bookings.return.submit');
        Route::get('/{booking}/return/verify', [BookingController::class, 'showVerifyForm'])->name('bookings.return.verify.show');
        Route::post('/{booking}/return/verify', [BookingController::class, 'verifyReturn'])->name('bookings.return.verify');
    });
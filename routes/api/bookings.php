<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingApiController;

Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingApiController::class, 'store'])->name('api.v1.bookings.store');
    Route::get('/history', [BookingApiController::class, 'userBookingHistory'])->name('api.v1.bookings.history');
});

Route::get('/approved-events', [BookingApiController::class, 'approvedEvents'])->name('api.v1.bookings.approved');

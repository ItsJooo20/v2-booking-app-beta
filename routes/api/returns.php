<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingReturnApiController;

Route::prefix('bookings')->group(function () {
    Route::post('{booking}/return', [BookingReturnApiController::class, 'submitReturn'])
        ->name('api.v1.bookings.return.submit');

    // Route::get('{booking}/return', [BookingReturnApiController::class, 'showReturn'])
        // ->middleware('auth:sanctum')
        // ->name('api.v1.bookings.return.show');

    Route::put('{booking}/return/verify', [BookingReturnApiController::class, 'verifyReturn'])
        ->name('api.v1.bookings.return.verify');
});


<?php

use App\Mail\testMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SendNotifApi;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\FirebasePushController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');

    Route::prefix('notifications')->group(function () {
        Route::post('/register-token', [NotificationController::class, 'registerToken']);
        Route::post('/send-to-all', [NotificationController::class, 'sendToAll']);
        Route::post('/send-to-user/{userId}', [NotificationController::class, 'sendToUser']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('v1')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/user/change-password', [AuthController::class, 'changePassword']);
        Route::post('/user/change-phone', [AuthController::class, 'changePhoneNumber']);
        Route::post('/user/profile-image', [AuthController::class, 'updateProfileImage']);
        Route::delete('/user/profile-image', [AuthController::class, 'removeProfileImage']);

        require __DIR__.'/api/bookings.php';
        require __DIR__.'/api/facilities.php';
        require __DIR__.'/api/returns.php';
    });

});

require __DIR__.'/auth.php';
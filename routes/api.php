<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification routes
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify'); // This should match the verification.url in config

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Add other protected API routes here
});
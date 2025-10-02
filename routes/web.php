<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityItemController;
use App\Http\Controllers\FacilityCategoryController;
use Kreait\Laravel\Firebase\Facades\Firebase;

Route::get('/test-firebase', function () {
    try {
        $messaging = Firebase::messaging();
        return 'Firebase connected successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('welcome');
});

// Admin routes with role-based access control
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    require __DIR__.'/admin/dashboard.php';
    require __DIR__.'/admin/users.php';
    require __DIR__.'/admin/facilities.php';
    require __DIR__.'/admin/bookings.php';
    require __DIR__.'/admin/reports.php';
});

require __DIR__.'/auth.php';

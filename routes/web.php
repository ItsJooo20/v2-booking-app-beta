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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__.'/admin/dashboard.php';
    require __DIR__.'/admin/users.php';
    require __DIR__.'/admin/facilities.php';
    require __DIR__.'/admin/bookings.php';
    require __DIR__.'/admin/reports.php';

});

require __DIR__.'/auth.php';

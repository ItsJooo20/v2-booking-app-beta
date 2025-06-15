<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityItemController;
use App\Http\Controllers\FacilityCategoryController;

    Route::resource('facility-categories', FacilityCategoryController::class);
    Route::resource('facilities', FacilityController::class);
    Route::resource('facility-items', FacilityItemController::class);
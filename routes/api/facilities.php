<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacilityApiController;

Route::prefix('facilities')->group(function () {
    Route::get('/categories', [FacilityApiController::class, 'categories'])->name('api.v1.facilities.categories');
    Route::get('/', [FacilityApiController::class, 'facilities'])->name('api.v1.facilities.index');

    Route::prefix('items')->group(function () {
        Route::get('/', [FacilityApiController::class, 'items'])->name('api.v1.facilities.items.index');
        Route::get('/{item}', [FacilityApiController::class, 'itemDetails'])->name('api.v1.facilities.items.show');
    });
});


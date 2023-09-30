<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CurrentPriceController;
use App\Http\Controllers\Admin\DiseaseOutbreakController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MarketItemController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\SoilRequirementController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('events', EventController::class);
    Route::apiResource('slides', SlideController::class);
    Route::apiResource('prices', CurrentPriceController::class);
    Route::apiResource('marketplace', MarketItemController::class);
    Route::apiResource('announcements', AnnouncementController::class);
    Route::apiResource('soil/requirements', SoilRequirementController::class);
    Route::apiResource('disease/outbreaks', DiseaseOutbreakController::class);
});

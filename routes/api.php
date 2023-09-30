<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CurrentPriceController;
use App\Http\Controllers\DiseaseOutbreakController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MarketItemController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\SoilRequirementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

if (file_exists(base_path('routes/api'))) {
    array_filter(File::files(base_path('routes/api')), function (\Symfony\Component\Finder\SplFileInfo $file) {
        if ($file->getExtension() === 'php') {
            Route::middleware('api')->group($file->getPathName());
        }
    });
}

Route::get('/init', function () {
    return (new \App\Http\Controllers\Controller())->buildResponse([
        'message' => 'OK',
        'status' => 'success',
        'status_code' => 200,
        'settings' => collect(config('settings'))->except(['permissions', 'system']),
        'csrf_token' => csrf_token(),
    ]);
});

Route::middleware('api')->group(base_path('routes/auth.php'));

Route::apiResource('users', UserController::class)->only(['index', 'show']);
Route::apiResource('prices', CurrentPriceController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->only(['index', 'show']);
Route::apiResource('slides', SlideController::class)->only(['index', 'show']);
Route::apiResource('diseases', DiseaseOutbreakController::class)->only(['index', 'show']);
Route::apiResource('marketplace', MarketItemController::class);
Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'show']);
Route::apiResource('soil/requirements', SoilRequirementController::class)->only(['index', 'show']);

Route::get('overview', [OverviewController::class, 'index']);
Route::get('overview/locations', [OverviewController::class, 'locations']);

Route::middleware('auth:sanctum')->prefix('account')->name('account.')->group(function () {
    Route::apiResource('/', AccountController::class)
        ->only(['index', 'update', 'store'])
        ->parameter('', 'user');
});

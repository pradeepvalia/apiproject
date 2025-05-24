<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\Api\EncryptionController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/encryption/public-key', [EncryptionController::class, 'getPublicKey']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Events routes
    Route::apiResource('events', EventController::class);

    // Users routes
    Route::apiResource('users', UserController::class);

    // Donations routes
    Route::apiResource('donations', DonationController::class);
    Route::get('/donations/total', [DonationController::class, 'getTotalDonations']);
    Route::get('/donations/recent', [DonationController::class, 'getRecentDonations']);

    // Email template routes
    Route::get('/email-template', [EmailTemplateController::class, 'show']);
    Route::put('/email-template', [EmailTemplateController::class, 'update']);

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'getStatistics']);
        Route::get('/monthly-stats', [DashboardController::class, 'getMonthlyStats']);
        Route::get('/daily-stats', [DashboardController::class, 'getDailyStats']);
        Route::get('/payment-methods', [DashboardController::class, 'getPaymentMethodStats']);
        Route::get('/recent-donations', [DashboardController::class, 'getRecentDonations']);
    });

    // Gallery routes
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('galleries', GalleryController::class);

    // Suggestion box routes
    Route::post('/suggestions', [SuggestionController::class, 'store']); // Public route for submitting suggestions

    // Admin routes for suggestions
    Route::get('/suggestions', [SuggestionController::class, 'index']);
    Route::get('/suggestions/{suggestion}', [SuggestionController::class, 'show']);
    Route::post('/suggestions/{suggestion}/mark-as-read', [SuggestionController::class, 'markAsRead']);
    Route::post('/suggestions/{suggestion}/mark-as-unread', [SuggestionController::class, 'markAsUnread']);
    Route::delete('/suggestions/{suggestion}', [SuggestionController::class, 'destroy']);
});

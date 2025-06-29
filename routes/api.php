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
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\VisitorController;

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
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/encryption/public-key', [EncryptionController::class, 'getPublicKey']);
Route::post('/suggestions', [SuggestionController::class, 'store']); // Public route for submitting suggestions
Route::get('/events', [EventController::class, 'index']); // Public route for listing events
Route::get('/events/active', [EventController::class, 'publicList']); // Public route for listing active events
Route::get('/galleries', [GalleryController::class, 'index']); // Public route for listing galleries
Route::get('/galleries/active', [GalleryController::class, 'publicList']); // Public route for listing active galleries
Route::get('/categories/active', [CategoryController::class, 'publicList']); // Public route for listing active categories
Route::post('/donations', [DonationController::class, 'store']); // Public route for making donations
Route::get('/events/{event}', [EventController::class, 'show']); // Public route for showing events

// Public Library routes
Route::get('/libraries', [LibraryController::class, 'index']);
Route::get('/libraries/{library}', [LibraryController::class, 'show']);
Route::get('/libraries/{library}/download', [LibraryController::class, 'download']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // User Profile routes
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'changePassword']);

    // Events routes (except index)
    Route::controller(EventController::class)->group(function () {
        Route::post('/events', 'store');
        // Route::get('/events/{event}', 'show');
        Route::put('/events/{event}', 'update');
        Route::delete('/events/{event}', 'destroy');
    });

    // Users routes
    Route::apiResource('users', UserController::class);

    // Donations routes (except store)
    Route::controller(DonationController::class)->group(function () {
        Route::get('/donations', 'index');
        Route::get('/donations/{donation}', 'show');
        Route::put('/donations/{donation}', 'update');
        Route::delete('/donations/{donation}', 'destroy');
        Route::get('/donations/total', 'getTotalDonations');
        Route::get('/donations/recent', 'getRecentDonations');
    });

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
        Route::get('/counts', [DashboardController::class, 'getCounts']);
    });

    // Gallery routes (except index)
    Route::controller(GalleryController::class)->group(function () {
        Route::post('/galleries', 'store');
        Route::get('/galleries/{gallery}', 'show');
        Route::put('/galleries/{gallery}', 'update');
        Route::delete('/galleries/{gallery}', 'destroy');
    });

    // Categories routes
    Route::apiResource('categories', CategoryController::class);

    // Admin routes for suggestions
    Route::get('/suggestions', [SuggestionController::class, 'index']);
    Route::get('/suggestions/{suggestion}', [SuggestionController::class, 'show']);
    Route::post('/suggestions/{suggestion}/mark-as-read', [SuggestionController::class, 'markAsRead']);
    Route::post('/suggestions/{suggestion}/mark-as-unread', [SuggestionController::class, 'markAsUnread']);
    Route::delete('/suggestions/{suggestion}', [SuggestionController::class, 'destroy']);

    // Library routes (protected operations)
    Route::post('/libraries', [LibraryController::class, 'store']);
    Route::put('/libraries/{library}', [LibraryController::class, 'update']);
    Route::delete('/libraries/{library}', [LibraryController::class, 'destroy']);

    // Activity Log routes
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index']);
        Route::get('/{activityLog}', [ActivityLogController::class, 'show']);
        Route::get('/modules/list', [ActivityLogController::class, 'getModules']);
        Route::get('/actions/list', [ActivityLogController::class, 'getActions']);
    });

    // Visitor Tracking API
    Route::prefix('visitors')->group(function () {
        Route::post('track', [VisitorController::class, 'track']);
        Route::get('stats', [VisitorController::class, 'stats']);
        Route::get('daily-stats', [VisitorController::class, 'dailyStats']);
        Route::get('top-pages', [VisitorController::class, 'topPages']);
    });
});

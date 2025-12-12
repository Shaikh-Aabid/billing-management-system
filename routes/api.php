<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\EwayBillController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Parties
    Route::get('/parties/all', [PartyController::class, 'all']);
    Route::apiResource('/parties', PartyController::class);

    // Products
    Route::get('/products/all', [ProductController::class, 'all']);
    Route::apiResource('/products', ProductController::class);

    // Bills
    Route::get('/bills/stats', [BillController::class, 'stats']);
    Route::get('/bills/{bill}/pdf', [BillController::class, 'pdf']);
    Route::post('/bills/{bill}/eway-bill', [EwayBillController::class, 'store']);
    Route::apiResource('/bills', BillController::class);

    // E-Way Bills
    Route::get('/eway-bills', [EwayBillController::class, 'index']);
    Route::get('/eway-bills/{ewayBill}', [EwayBillController::class, 'show']);
    Route::get('/eway-bills/{ewayBill}/pdf', [EwayBillController::class, 'pdf']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::put('/settings/business', [SettingsController::class, 'updateBusiness']);
    Route::put('/settings/account', [SettingsController::class, 'updateAccount']);
    Route::put('/settings/password', [SettingsController::class, 'updatePassword']);
    Route::put('/settings/bill', [SettingsController::class, 'updateBillSettings']);
});

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\LayupController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes for version 1
Route::prefix('v1')->group(function () {
    // Supplier routes
    Route::apiResource('suppliers', SupplierController::class);
    // Layups Nested Routes
    Route::apiResource('suppliers.layups', LayupController::class);
});

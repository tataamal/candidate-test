<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// API routes for version 1
Route::prefix("v1")->group(function () {
    // Supplier routes
    Route::apiResource('suppliers', SupplierController::class);
});



<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierTransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes for version 1
Route::prefix('v1')->group(function () {

    // Public — tidak butuh token
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Protected — butuh token
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('suppliers', SupplierController::class);
        Route::apiResource('suppliers.layups', LayupController::class);
        Route::apiResource('suppliers.layups.layers', LayerController::class);
        Route::get('suppliers/{supplier}/export', [SupplierTransferController::class, 'export']);
        Route::get('suppliers/{supplier}/import-template', [SupplierTransferController::class, 'template']);
        Route::post('suppliers/{supplier}/import', [SupplierTransferController::class, 'import']);
    });

});

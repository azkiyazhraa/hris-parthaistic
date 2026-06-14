<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PenggajianApiController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    Route::get('/penggajian', [PenggajianApiController::class, 'index']);
    Route::get('/penggajian/summary', [PenggajianApiController::class, 'getSummary']);
    Route::get('/penggajian/karyawan/{karyawan_id}', [PenggajianApiController::class, 'getByKaryawan']);
    Route::get('/penggajian/{id}', [PenggajianApiController::class, 'show']);
    Route::post('/penggajian', [PenggajianApiController::class, 'store']);
    Route::put('/penggajian/{id}', [PenggajianApiController::class, 'update']);
    Route::patch('/penggajian/{id}', [PenggajianApiController::class, 'update']);
    Route::delete('/penggajian/{id}', [PenggajianApiController::class, 'destroy']);
    Route::patch('/penggajian/{id}/status', [PenggajianApiController::class, 'updateStatus']);
    Route::post('/penggajian/{id}/send-payslip', [PenggajianApiController::class, 'sendPayslip']);
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found. Check API documentation.'
    ], 404);
});

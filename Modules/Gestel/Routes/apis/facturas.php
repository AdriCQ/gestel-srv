<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\FacturaController;

// Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/', [FacturaController::class, 'csv']);
Route::post('/', [FacturaController::class, 'uploadZip']);
// });

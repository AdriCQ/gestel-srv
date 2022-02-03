<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\FacturaController;

// Route::middleware(['auth:sanctum'])->group(function () {
// Route::get('/', [FacturaController::class, 'list']);
Route::post('/etecsa', [FacturaController::class, 'uploadEtecsa']);
// Route::get('/{id}', [FacturaController::class, 'find']);
// Route::get('/{id}/get', [FacturaController::class, 'get']);
// Route::get('/{id}/test', [FacturaController::class, 'test']);
// });

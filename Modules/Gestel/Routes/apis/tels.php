<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\TelController;

Route::get('/', [TelController::class, 'list']);
Route::get('/search', [TelController::class, 'search']);
Route::get('/{id}', [TelController::class, 'get']);


Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('/', [TelController::class, 'create']);
  Route::put('/{id}', [TelController::class, 'update']);
  Route::delete('/{id}', [TelController::class, 'remove']);
});

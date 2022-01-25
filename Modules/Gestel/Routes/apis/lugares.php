<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\LugarController;

Route::get('/', [LugarController::class, 'list']);

Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('/', [LugarController::class, 'create']);
  Route::delete('/:id', [LugarController::class, 'remove']);
  Route::put('/:id', [LugarController::class, 'update']);
});

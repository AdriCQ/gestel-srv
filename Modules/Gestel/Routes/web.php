<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\ReportController;

Route::prefix('gestel')->group(function () {
  Route::prefix('reports')->group(function () {
    Route::get('/sobregiro', [ReportController::class, 'telPasados']);
  });
});

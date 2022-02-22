<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\StatsController;

Route::get('/preload', [StatsController::class, 'preload']);
Route::get('/pasados', [StatsController::class, 'telPasados']);

<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\FacturaController;

Route::get('/', [FacturaController::class, 'list']);
Route::get('/seed', [FacturaController::class, 'seedTest']);
Route::post('/etecsa', [FacturaController::class, 'uploadEtecsa']);
Route::get('/etecsa', [FacturaController::class, 'telsEtecsa']);

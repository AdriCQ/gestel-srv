<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\FacturaController;

Route::post('/etecsa', [FacturaController::class, 'uploadEtecsa']);
Route::get('/etecsa', [FacturaController::class, 'telsEtecsa']);
Route::get('/', [FacturaController::class, 'seedTest']);

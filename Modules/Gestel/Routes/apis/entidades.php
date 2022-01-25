<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\EntidadController;

Route::get('/', [EntidadController::class, 'list']);

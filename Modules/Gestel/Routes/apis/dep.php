<?php

use Illuminate\Support\Facades\Route;
use Modules\Gestel\Http\Controllers\DepController;


Route::get('/', [DepController::class, 'list']);
Route::get('/{id}', [DepController::class, 'find']);

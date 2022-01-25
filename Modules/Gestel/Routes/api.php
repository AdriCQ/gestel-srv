<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/gestel')->group(function () {
  // Lugares Api
  Route::prefix('/lugares')->group(__DIR__ . '/apis/lugares.php');
  Route::prefix('/dep')->group(__DIR__ . '/apis/dep.php');
  Route::prefix('/entidades')->group(__DIR__ . '/apis/entidades.php');
  Route::prefix('/facturas')->group(__DIR__ . '/apis/facturas.php');
  Route::prefix('/tels')->group(__DIR__ . '/apis/tels.php');
});

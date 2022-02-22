<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGestelCentroPagosTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('gestel_centro_pagos', function (Blueprint $table) {
      $table->id();
      $table->string('nombre');
    });

    Schema::table('gestel_entidades', function (Blueprint $table) {
      $table->foreignId('centro_pago_id')->constrained('gestel_centro_pagos')->onDelete('CASCADE');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('gestel_centro_pagos');
  }
}

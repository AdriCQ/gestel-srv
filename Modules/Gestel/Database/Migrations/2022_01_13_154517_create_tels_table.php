<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('gestel_tels', function (Blueprint $table) {
      $table->id();
      $table->string('tel')->unique();
      $table->string('servicio')->default('AUTOMATICO');
      $table->string('tipo')->default('PRIVADO');
      $table->string('comentario')->nullable();
      $table->unsignedDecimal('presupuesto', 8, 2)->default(0);
      $table->timestamp('fecha_alta')->default(now());
      $table->timestamp('fecha_baja')->nullable();
      $table->foreignId('cargo_id')->constrained('gestel_cargos')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('gestel_tels');
  }
}

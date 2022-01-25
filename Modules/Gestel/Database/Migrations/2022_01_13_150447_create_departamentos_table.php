<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartamentosTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('gestel_departamentos', function (Blueprint $table) {
      $table->id();
      $table->string('nombre');
      $table->foreignId('entidad_id')->constrained('gestel_entidades')->onDelete('cascade');
      $table->foreignId('lugar_id')->constrained('gestel_lugares')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('gestel_departamentos');
  }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelAutomaticFacturationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('gestel_automatic_facturations', function (Blueprint $table) {
      $table->id();
      $table->string('telf');
      $table->unsignedDecimal('importe', 8, 2)->default(0);
      $table->string('noft');
      $table->unsignedTinyInteger('dia')->nullable();
      $table->unsignedTinyInteger('mes');
      $table->unsignedTinyInteger('year');
      $table->string('dest')->nullable();
      $table->string('slla')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('gestel_automatic_facturations');
  }
}

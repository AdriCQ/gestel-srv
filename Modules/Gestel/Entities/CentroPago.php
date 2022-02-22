<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CentroPago extends Model
{
  use HasFactory;

  protected $table = 'gestel_centro_pagos';
  protected $guarded = ['id'];
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */
  /**
   * entidades
   * @return HasMany
   */
  public function entidades(): HasMany
  {
    return $this->hasMany(Entidad::class, 'centro_pago_id', 'id');
  }
}

<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entidad extends Model
{
  // use HasFactory;

  protected $table = 'gestel_entidades';
  protected $guarded = ['id'];
  public $timestamps = false;
  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */
  /**
   * centroPago
   * @return BelongsTo
   */
  public function centroPago(): BelongsTo
  {
    return $this->belongsTo(CentroPago::class, 'centro_pago_id', 'id');
  }
  /**
   * departamentos
   * @return HasMany
   */
  public function departamentos(): HasMany
  {
    return $this->hasMany(Departamento::class, 'entidad_id', 'id');
  }
}

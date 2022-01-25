<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
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

  public function departamentos()
  {
    return $this->hasMany(Departamento::class, 'entidad_id', 'id');
  }
}

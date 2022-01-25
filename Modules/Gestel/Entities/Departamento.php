<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
  // use HasFactory;
  protected $table = 'gestel_departamentos';
  protected $guarded = ['id'];
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */

  public function entidad()
  {
    return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
  }

  public function lugar()
  {
    return $this->belongsTo(Lugar::class, 'lugar_id', 'id');
  }

  public function cargos()
  {
    return $this->hasMany(Cargo::class, 'dep_id', 'id');
  }
}

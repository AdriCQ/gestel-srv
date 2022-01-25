<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cargo extends Model
{
  // use HasFactory;

  protected $table = 'gestel_cargos';
  protected $guarded = ['id'];
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */
  /**
   * 
   */
  public function departamento()
  {
    return $this->belongsTo(Departamento::class, 'dep_id', 'id');
  }
  /**
   * 
   */
  public function telefonos()
  {
    return $this->hasMany(Tel::class, 'cargo_id', 'id');
  }
}

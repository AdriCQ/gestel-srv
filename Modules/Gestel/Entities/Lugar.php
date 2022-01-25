<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lugar extends Model
{
  // use HasFactory;

  protected $table = 'gestel_lugares';
  protected $guarded = ['id'];
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Relation
   * -----------------------------------------
   */
  public function departamentos()
  {
    return $this->hasMany(Departamento::class, 'lugar_id', 'id');
  }
}

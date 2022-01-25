<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tel extends Model
{
  // use HasFactory;

  protected $table = 'gestel_tels';
  protected $guarded = ['id'];
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Relations
   * -----------------------------------------
   */
  public function cargo()
  {
    return $this->belongsTo(Cargo::class, 'cargo_id', 'id');
  }
}

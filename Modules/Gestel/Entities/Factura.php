<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @property int id
 * @property string path
 * @property string fecha
 * @property json resumen
 */
class Factura extends Model
{

  // use HasFactory;

  protected $table = 'gestel_facturas';
  protected $guarded = ['id'];
  public $timestamps = false;
}

<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TelAutoFactura extends Model
{
  use HasFactory;
  protected $guarded = ['id'];
  protected $table = 'gestel_automatic_facturations';
  public $timestamps = false;
}

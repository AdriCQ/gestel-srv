<?php

namespace Modules\Gestel\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class TelAutoFactura extends Model
{
  use HasFactory;
  protected $guarded = ['id'];
  protected $table = 'gestel_automatic_facturations';
  public $timestamps = false;

  /**
   * -----------------------------------------
   *	Helpers
   * -----------------------------------------
   */

  /**
   * paths
   * @param path string
   * @param asStoragePath string
   * @return string
   */
  static public function paths($path = '', $asStoragePath = false)
  {
    $ZIP_STORAGE = 'modules/gestel/';
    $ZIP_STORAGE_PATH = 'app/modules/gestel/';
    return $asStoragePath ? $ZIP_STORAGE_PATH . $path : $ZIP_STORAGE . $path;
  }


  /**
   * Preload
   * @param int $mes
   * @param int $year
   * @return array
   */
  static public function preload(int $mes, int $year)
  {
    $jsonPath = self::paths('preload/preload_' . $year . '_' . $mes . '.json');
    if (Storage::exists($jsonPath)) {
      return json_decode(Storage::get($jsonPath), true);
    }

    $preloadData = TelAutoFactura::query()->where(['mes' => $mes, 'year' => $year])->groupBy('telf')
      ->selectRaw('telf, sum(importe) as total_importe')
      ->get(['telf'])->toArray();
    Storage::put($jsonPath, json_encode($preloadData));
    return $preloadData;
  }
  /**
   * _preloadFind
   * @param string $tel
   * @param int $mes
   * @param int $year
   */
  static public function preloadFind(string $tel, int $mes, int $year)
  {
    $preload = self::preload($mes, $year);
    foreach ($preload as $value) {
      if ($tel == $value['telf'])
        return $value;
    }
    return null;
  }
}

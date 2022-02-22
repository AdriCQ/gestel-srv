<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Tel;
use Modules\Gestel\Entities\TelAutoFactura;

class StatsController extends Controller
{
  /**
   * Pasados
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function telPasados(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'mes' => ['required', 'integer'],
      'year' => ['required', 'integer'],
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    // TODO: Optimize query
    $tels = Tel::query()->get(['id', 'telf', 'presupuesto'])->toArray();
    $pasados = [];
    foreach ($tels as $tel) {
      $preloadFind = TelAutoFactura::preloadFind($tel['telf'], $validator['mes'], $validator['year']);
      if ($preloadFind) {
        if ($preloadFind['total_importe'] > $tel['presupuesto']) {
          // if ($preloadFind['total_importe'] > 850) {
          $tel['dif'] = $preloadFind['total_importe'] - $tel['presupuesto'];
          array_push($pasados, $tel);
        }
      }
    }
    return $this->sendResponse($pasados);
  }
  /**
   * Preload
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function preload(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'mes' => ['required', 'integer'],
      'year' => ['required', 'integer'],
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    return $this->sendResponse(TelAutoFactura::preload($validator['mes'], $validator['year']));
  }
}

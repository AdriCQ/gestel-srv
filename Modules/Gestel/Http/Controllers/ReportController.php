<?php

namespace Modules\Gestel\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Tel;
use Modules\Gestel\Entities\TelAutoFactura;

class ReportController extends Controller
{
  /**
   * telPasados
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
      return $this->sendError($validator->errors()->toArray(), ['Los datos no son correctos']);
    }
    $validator = $validator->validate();
    $tels = Tel::query()->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad'])->get()->toArray();
    $pasados = [];
    $totalSobregiro = 0;
    $counterTels = 0;
    foreach ($tels as $tel) {
      $preloadFind = TelAutoFactura::preloadFind($tel['telf'], $validator['mes'], $validator['year']);
      if ($preloadFind) {
        if ($preloadFind['total_importe'] > $tel['presupuesto']) {
          // if ($preloadFind['total_importe'] > 850) {
          $tel['dif'] = $preloadFind['total_importe'] - $tel['presupuesto'];
          $totalSobregiro += $tel['dif'];
          $counterTels++;
          array_push($pasados, $tel);
        }
      }
    }
    $pdf = Pdf::loadView('gestel::reports.sobregiro', [
      'tels' => $pasados,
      'mes' => $validator['mes'],
      'year' => $validator['year'],
      'totalSobregiro' => $totalSobregiro,
      'counterTels' => $counterTels
    ]);
    return $pdf->download('Reporte-Sobregiro-' . $validator['mes'] . '-' . $validator['year'] . '.pdf');
  }
}

<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Factura;

class FacturaController extends Controller
{
  private $ZIP_STORAGE = 'modules/gestel/facturas/';
  private $ZIP_STORAGE_PATH = 'app/modules/gestel/facturas/';

  /**
   * CVS
   * @return Illuminate\Http\JsonResponse
   */
  public function csv()
  {
    return $this->sendResponse($this->getData('1010000000244.csv'));
  }
  /**
   * Remove
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function remove(int $id)
  {
    $factura = Factura::query()->find($id);
    if (!$factura)
      return $this->sendError(null, ['Factura no existe']);

    $path = $this->ZIP_STORAGE_PATH . 'factura-' . $factura->fecha . '.zip';
    $extractPath = $this->ZIP_STORAGE_PATH . 'factura-' . $factura->fecha;
    Storage::delete($path);
    Storage::deleteDirectory($extractPath);
    return $factura->delete() ? $this->sendResponse(null, ['Factura eliminada']) : $this->sendError($factura->error, ['Error eliminando factura']);
  }

  /**
   * UploadZip
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function uploadZip(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'factura' => ['required', 'file'],
      'fecha' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    // Upload File
    $fileName = 'factura_' . $validator['fecha'] . '.zip';
    if (!$request->file('factura')->storeAs($this->ZIP_STORAGE, $fileName))
      return $this->sendError(null, ['No se pudo guardar el archivo']);
    // If Exists
    if (Factura::query()->where('fecha', $validator['fecha'])->first()) {
      $factura = Factura::query()->where('fecha', $validator['fecha'])->first();
      return $factura->update(['resumen' => null]) ? $this->sendResponse($factura) : $this->sendError($factura->errors, ['No se pudo guardar']);
    }
    $factura = new Factura([
      'fecha' => $validator['fecha'],
      'resumen' => null
    ]);

    return $factura->save() ? $this->sendResponse($factura) : $this->sendError($factura->errors, ['No se pudo guardar']);
  }
  /**
   * toJson
   */
  private function toJson($csvFile)
  {
    $fileHandler = fopen(storage_path($this->ZIP_STORAGE_PATH . $csvFile), 'r');
    while (!feof($fileHandler)) {
      $line[] = fgetcsv($fileHandler, 0, ',');
    }
    $datas = [];
    $column = [];
    foreach ($line[0] as $lineName) {
      array_push($column, $lineName);
    }
    foreach ($line as $lKey => $lVal) {
      if ($lKey > 0) {
        foreach ($column as $colKey => $col) {
          // $datas[$lKey - 1][$col] = $colKey;
          if (isset($lVal[$colKey]))
            $datas[$lKey - 1][$col] = $lVal[$colKey] ?? '';
        }
      }
    }
    fclose($fileHandler);
    return $datas;
  }

  private function getData($file)
  {
    $data = [];
    $totalImport = 0;
    // if (!str_contains($file, '.csv'))
    //   continue;
    $fileHandler = fopen(storage_path($this->ZIP_STORAGE_PATH . $file), 'r');
    while (!feof($fileHandler)) {
      $line[] = fgetcsv($fileHandler, 0, ',');
    }
    $indexes = [];
    // getting indexes
    foreach ($line[0] as $key => $val) {
      if ($val === 'TELF')
        $indexes['TELF'] = $key;
      if ($val === 'IMPT')
        $indexes['IMPT'] = $key;
      if ($val === 'CARGO')
        $indexes['CARGO'] = $key;
    }

    $tels = [];
    // Walk throutgh data
    foreach ($line as $lKey => $lValue) {
      if ($lKey === 0)
        continue;
      if (isset($lValue[$indexes['TELF']]) && isset($lValue[$indexes['IMPT']])) {
        array_push($tels, [
          'TELF' => $lValue[$indexes['TELF']],
          'IMPT' => $lValue[$indexes['IMPT']],
          'CARGO' => $lValue[$indexes['CARGO']],
        ]);
      }
    }

    // Order Data
    foreach ($tels as $tVal) {
      if (!is_numeric($tVal['IMPT']) || $tVal['CARGO'] === 'ALOC')
        continue;
      $prevData = count($data) - 1;
      // $totalImport += round($tVal['IMPT'], 2, PHP_ROUND_HALF_UP);
      if ($prevData >= 0 && $data[$prevData]['TELF'] === $tVal['TELF']) {
        $data[$prevData]['IMPT'] += round($tVal['IMPT'], 2);
      } else {
        if (is_numeric($tVal['IMPT']))
          array_push($data, $tVal);
      }
    }
    fclose($fileHandler);
    return [$data];
  }
}

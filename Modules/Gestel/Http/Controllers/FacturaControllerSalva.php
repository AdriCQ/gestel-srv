<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Gestel\Imports\TelAutoFacturaImport;
use ZanySoft\Zip\Zip;

class FacturaController extends Controller
{
  private $ZIP_STORAGE = 'modules/gestel/facturas/';
  private $ZIP_STORAGE_PATH = 'app/modules/gestel/facturas/';

  /**
   * Test
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function test(int $id, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'csv' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    $fact = Factura::query()->find($id);
    if (!$fact)
      return $this->sendError(null, ['Factura no encontrada']);
    if (!Storage::exists($this->extractPath($fact)['STORAGE'] . '/' . $validator['csv']))
      return [];
    $file = $this->extractPath($fact)['STORAGE'] . '/' . $validator['csv'];
    $data = Excel::import(new TelAutoFacturaImport(2), $file);
    return $this->sendResponse($data);
  }

  /**
   * Find
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function find(int $id)
  {
    $fact = Factura::query()->find($id);
    if (!$fact)
      return $this->sendError(null, ['Factura no encontrada']);
    $extractZip = $this->extractPath($fact);
    $zipPath = $this->zipPath($fact);
    if (!Storage::exists($extractZip['STORAGE'])) {
      // Extract if doesnot exists
      $zip = Zip::open(storage_path($zipPath['STORAGE_PATH']));
      if (!$zip)
        return $this->sendError(null, ['NO existe el archivo']);
      $zip->extract(storage_path($extractZip['STORAGE_PATH']));
    }
    // List facturas in path
    $returnData = [];
    foreach (Storage::allFiles($extractZip['STORAGE']) as $factura) {
      $fileName = explode('/', $factura);

      array_push($returnData, $fileName[count($fileName) - 1]);
    }
    return $this->sendResponse($returnData);
  }

  /**
   * Get
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function get(int $id, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'csv' => ['required', 'string'],
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    $factura  =  Factura::query()->find($id);

    return $factura ? $this->getData($factura, $validator['csv']) : $this->sendError(null, ['No existe la factura']);
  }

  /**
   * list
   * @return Illuminate\Http\JsonResponse
   */
  public function list()
  {
    return $this->sendResponse(
      Factura::all()->toArray()
    );
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
    $extractPath = $this->extractPath($factura)['STORAGE'];
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
      'mes' => ['required', 'number']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    // If Exists
    if (Factura::query()->where('mes', $validator['mes'])->first()) {
      $factura = Factura::query()->where('mes', $validator['mes'])->first();
      return $factura->update(['resumen' => null]) ? $this->sendResponse($factura) : $this->sendError($factura->errors, ['No se pudo guardar']);
    }
    $factura = new Factura([
      'mes' => $validator['mes'],
      'resumen' => null
    ]);
    // Upload File
    $filePath = $this->zipPath($factura);
    if (!$request->file('factura')->storeAs($this->ZIP_STORAGE, $validator['mes'] . '.zip'))
      return $this->sendError(null, ['No se pudo guardar el archivo']);
    $zip = Zip::open(storage_path($filePath['STORAGE_PATH']));
    // Unzip
    $unzipPath = $this->extractPath($factura);
    if (!$zip->extract(storage_path($unzipPath['STORAGE_PATH'])))
      return $this->sendError(null, ['Error al descomprimir']);
    $zip->close();

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

  private function getData(Factura $factura, string $csv)
  {
    $data = [];
    $totalImport = 0;
    // if (!str_contains($file, '.csv'))
    //   continue;
    if (!Storage::exists($this->extractPath($factura)['STORAGE'] . '/' . $csv))
      return [];
    $fileName = $this->extractPath($factura)['STORAGE_PATH'] . '/' . $csv;
    $fileHandler = fopen(storage_path($fileName), 'r');
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
      $totalImport += round($tVal['IMPT'], 2, PHP_ROUND_HALF_UP);
      if ($prevData >= 0 && $data[$prevData]['TELF'] === $tVal['TELF']) {
        $data[$prevData]['IMPT'] += round($tVal['IMPT'], 2);
      } else {
        if (is_numeric($tVal['IMPT']))
          array_push($data, $tVal);
      }
    }
    fclose($fileHandler);
    return ['total' => $totalImport, 'tels' => $data];
  }
  /**
   * 
   */
  private function extractPath(Factura $factura)
  {
    $storagePath = $this->ZIP_STORAGE_PATH . $factura->fecha;
    $storage = $this->ZIP_STORAGE . $factura->fecha;
    return ['STORAGE' => $storage, 'STORAGE_PATH' => $storagePath];
  }
  /**
   * 
   */
  private function zipPath(Factura $fact)
  {
    $storagePath = $this->ZIP_STORAGE_PATH . $fact->fecha . '.zip';
    $storage = $this->ZIP_STORAGE . $fact->fecha . '.zip';
    return ['STORAGE' => $storage, 'STORAGE_PATH' => $storagePath];
  }
}

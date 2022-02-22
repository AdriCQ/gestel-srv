<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Gestel\Entities\Tel;
use Modules\Gestel\Entities\TelAutoFactura;
use Modules\Gestel\Imports\TelAutoFacturaImport;
use ZanySoft\Zip\Zip;

class FacturaController extends Controller
{
  /**
   * List
   * @return Illuminate\Http\JsonResponse
   */
  public function list()
  {
    $qry = TelAutoFactura::query()->groupBy('mes', 'year')->selectRaw('mes,year, sum(importe) as total_importe')->get();
    return $this->sendResponse($qry);
  }
  /**
   * Tels
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function telsEtecsa(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'mes' => ['required', 'integer'],
      'year' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    return $this->sendResponse(
      TelAutoFactura::query()->where(['mes' => $validator['mes'], 'year' => $validator['year']])->simplePaginate(50)
    );
  }
  /**
   * uploadEtecsa
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function uploadEtecsa(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'factura' => ['required', 'file'],
      'mes' => ['required', 'integer'],
      'year' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), []);
    }
    $validator = $validator->validate();
    // Check if exists
    if (TelAutoFactura::query()->where(['mes' => $validator['mes'], 'year' => $validator['year']])->exists()) {
      return $this->sendError(null, ['Ya existe la factura']);
    }
    // Upload zip
    $fileName = 'FACTURA_ETECSA_' . $validator['year'] . '_' . $validator['mes'];
    $zipFileName =  $fileName . '.zip';
    // Check if exists File and delete it
    if (
      Storage::exists(TelAutoFactura::paths($zipFileName))
      && !Storage::delete(TelAutoFactura::paths($zipFileName))
    )
      return $this->sendError(null, ['Error de permisos de directorios']);
    // Upload and save it
    if (!$request->file('factura')->storeAs(TelAutoFactura::paths(''), $zipFileName))
      return $this->sendError(null, ['No se pudo guardar el archivo']);
    // Unzip file
    $zip = Zip::open(storage_path(TelAutoFactura::paths($zipFileName, true)));
    $unzipPath = 'unzip/' . $fileName . '/';
    // Check if exists unzip Path and delete it
    if (
      Storage::exists(TelAutoFactura::paths($unzipPath))
      && !Storage::deleteDirectory(TelAutoFactura::paths($unzipPath))
    )
      return $this->sendError(null, ['Error de permisos de directorios']);
    // Extract 
    if (!$zip->extract(storage_path(TelAutoFactura::paths($unzipPath, true))))
      return $this->sendError(null, ['Error al descomprimir']);

    // Get all files
    $filesToImport = Storage::allFiles(TelAutoFactura::paths($unzipPath));
    // Insert on Database
    foreach ($filesToImport as $import) {
      Excel::import(new TelAutoFacturaImport($validator['mes'], $validator['year']), storage_path('app/' . $import));
    }
    return $this->sendResponse($filesToImport);
  }

  /**
   * -----------------------------------------
   *	Helpers
   * -----------------------------------------
   */

  /**
   * SeedTest
   */
  public function seedTest()
  {
    $telf = TelAutoFactura::query()->groupBy('telf')->get('telf')->toArray();
    foreach ($telf as $tel) {
      if (!Tel::query()->where('telf', $tel['telf'])->count()) {
        Tel::query()->insert([
          'telf' => $tel['telf'],
          'presupuesto' => 200,
          'cargo_id' => 1
        ]);
      }
    }
    return $this->sendResponse(Tel::query()->count());
  }
}

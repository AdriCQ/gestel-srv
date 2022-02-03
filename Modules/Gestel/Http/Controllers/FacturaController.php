<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Gestel\Entities\TelAutoFactura;
use Modules\Gestel\Imports\TelAutoFacturaImport;
use ZanySoft\Zip\Zip;

class FacturaController extends Controller
{
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
      Storage::exists($this->paths($zipFileName))
      && !Storage::delete($this->paths($zipFileName))
    )
      return $this->sendError(null, ['Error de permisos de directorios']);
    // Upload and save it
    if (!$request->file('factura')->storeAs($this->paths(''), $zipFileName))
      return $this->sendError(null, ['No se pudo guardar el archivo']);
    // Unzip file
    $zip = Zip::open(storage_path($this->paths($zipFileName, true)));
    $unzipPath = 'unzip/' . $fileName . '/';
    // Check if exists unzip Path and delete it
    if (
      Storage::exists($this->paths($unzipPath))
      && !Storage::deleteDirectory($this->paths($unzipPath))
    )
      return $this->sendError(null, ['Error de permisos de directorios']);
    // Extract 
    if (!$zip->extract(storage_path($this->paths($unzipPath, true))))
      return $this->sendError(null, ['Error al descomprimir']);

    // Get all files
    $filesToImport = Storage::allFiles($this->paths($unzipPath));
    // Insert on Database
    foreach ($filesToImport as $import) {
      Excel::import(new TelAutoFacturaImport($validator['mes'], $validator['year']), storage_path('app/' . $import));
    }
    return $this->sendResponse($filesToImport);
  }
  /**
   * paths
   * @param path string
   * @param asStoragePath string
   * @return string
   */
  private function paths($path = '', $asStoragePath = false)
  {
    $ZIP_STORAGE = 'modules/gestel/facturas/';
    $ZIP_STORAGE_PATH = 'app/modules/gestel/facturas/';
    return $asStoragePath ? $ZIP_STORAGE_PATH . $path : $ZIP_STORAGE . $path;
  }
}

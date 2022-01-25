<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Entidad;

class EntidadController extends Controller
{
  /**
   * list
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function list(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'deep' => ['nullable', 'boolean']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
      if (isset($validator['deep']) && $validator['deep'])
        return $this->sendResponse(Entidad::query()->with('departamentos', 'departamentos.cargos', 'departamentos.lugar')->get());
      return $this->sendResponse(Entidad::all());
    }
  }
}

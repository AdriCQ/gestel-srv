<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Departamento;

class DepController extends Controller
{
  /**
   * Create
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => ['required', 'string'],
      'lugar_id' => ['required', 'integer'],
      'entidad_id' => ['required', 'integer']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
      $dep = new Departamento($validator);
      return $dep->save() ? $this->sendResponse($dep) : $this->sendError($dep->errors, ['Error guardando departamento']);
    }
  }

  /**
   * Find
   * @param int id
   * @return Illuminate\Http\JsonResponse
   */
  public function find(int $id)
  {
    return $this->sendResponse(Departamento::query()->where('id', $id)->with(['cargos', 'cargos.telefonos', 'lugar', 'entidad'])->get());
  }

  /**
   * List
   * @return Illuminate\Http\JsonResponse
   */
  public function list()
  {
    return $this->sendResponse(Departamento::all());
  }
}

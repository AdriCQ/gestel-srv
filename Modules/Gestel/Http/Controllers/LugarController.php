<?php

namespace Modules\Gestel\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Lugar;

class LugarController extends Controller
{
  /**
   * create
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
      $lugar = new Lugar($validator);
      return $lugar->save() ? $this->sendResponse($lugar) : $this->sendError($lugar->errors->toArray());
    }
  }
  /**
   * List
   * @return Illuminate\Http\JsonResponse
   */
  public function list()
  {
    return $this->sendResponse(Lugar::all());
  }

  /**
   * Remove
   * @param int id
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function remove(int $id)
  {
    $lugar = Lugar::query()->find($id);
    if (!$lugar)
      return $this->sendError(null, ['Lugar no existe']);
    return $lugar->remove();
  }

  /**
   * Update
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function update(int $id, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nombre' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
    }
  }
}

<?php

namespace Modules\Gestel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Gestel\Entities\Cargo;
use Modules\Gestel\Entities\Departamento;
use Modules\Gestel\Entities\Entidad;
use Modules\Gestel\Entities\Lugar;
use Modules\Gestel\Entities\Tel;

class TelController extends Controller
{
  /**
   * Create
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'tel' => ['required', 'string', 'unique:gestel_tels'],
      'servicio' => ['required', 'in:AUTOMATICO,EXTENSION'],
      'tipo' => ['required', 'in:PRIVADO,PUBLICO'],
      'presupuesto' => ['required', 'numeric'],
      'cargo_id' => ['required', 'integer'],
      'comentario' => ['nullable', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    }
    $validator = $validator->validate();
    $tel = new Tel($validator);
    return $tel->save() ? $this->sendResponse($tel) : $this->sendError($tel->errors, ['No se pudo guardar']);
  }
  /**
   * get
   */
  public function get(int $id)
  {
    return $this->sendResponse(Tel::query()->where('id', $id)->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->first());
  }

  /**
   * List
   * @return Illuminate\Http\JsonResponse
   */
  public function list()
  {
    return $this->sendResponse(Tel::all());
  }

  /**
   * Remove
   * @return Illuminate\Http\JsonResponse
   */
  public function remove(int $id)
  {
    return Tel::query()->find($id)->delete() ? $this->sendResponse(null, ['Teléfono eliminado']) : $this->sendError(null, ['No se pudo eliminar']);
  }

  /**
   * search
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function search(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'search' => ['nullable', 'string'],
      'type' => ['required', 'in:global,tel,cargo,departamento,entidad,lugar']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
      $qry = Tel::query();
      if (!isset($validator['search']) || $validator['search'] === '') {
        return $this->sendResponse($qry->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get());
      }
      $validator['search'] = strtoupper($validator['search']);
      switch ($validator['type']) {
        case 'cargo':
          $cargQry = Cargo::query()->where('nombre', 'like', '%' . $validator['search'] . '%')->get(['id'])->toArray();
          return $this->sendResponse(
            $qry->whereIn('cargo_id', $cargQry)->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get(),
            ['Tels']
          );
        case 'departamento':
          $depQry = Departamento::query()->where('nombre', 'like', '%' . $validator['search'] . '%')->get(['id'])->toArray();
          $cargoDep = Cargo::query()->whereIn('dep_id', $depQry)->get(['id'])->toArray();
          return $this->sendResponse(
            $qry->whereIn('cargo_id', $cargoDep)->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get()
          );
        case 'entidad':
          $entQry = Entidad::query()->where('nombre', 'like', '%' . $validator['search'] . '%')->get(['id'])->toArray();
          $depQry = Departamento::query()->whereIn('entidad_id', $entQry)->get(['id'])->toArray();
          $cargoDep = Cargo::query()->whereIn('dep_id', $depQry)->get(['id'])->toArray();
          return $this->sendResponse(
            $qry->whereIn('cargo_id', $cargoDep)->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get()
          );
        case 'lugar':
          $lugarQry = Lugar::query()->where('nombre', 'like', '%' . $validator['search'] . '%')->get(['id'])->toArray();
          $depQry = Departamento::query()->whereIn('lugar_id', $lugarQry)->get(['id'])->toArray();
          $cargoDep = Cargo::query()->whereIn('dep_id', $depQry)->get(['id'])->toArray();
          return $this->sendResponse(
            $qry->whereIn('cargo_id', $cargoDep)->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get()
          );
        default:
          return $this->sendResponse(
            $qry->where('tel', 'like', '%' . $validator['search'] . '%')->with(['cargo', 'cargo.departamento', 'cargo.departamento.entidad', 'cargo.departamento.lugar'])->get(),
            ['Tels']
          );
      }
      $lugQry = Lugar::query()->where('nombre', 'like', '%' . $validator['search'] . '%');
      $entQry = Entidad::query()->where('nombre', 'like', '%' . $validator['search'] . '%');
      return $this->sendResponse([], ['No Data']);
    }
  }
  /**
   * Update
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function update(int $id, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'servicio' => ['nullable', 'in:AUTOMATICO,EXTENSION'],
      'tipo' => ['nullable', 'in:PRIVADO,PUBLICO'],
      'presupuesto' => ['nullable', 'numeric'],
      'comentario' => ['nullable', 'string'],
      'cargo_id' => ['nullable', 'integer'],
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), [], 404);
    } else {
      $validator = $validator->validate();
      $tel = Tel::query()->find($id);
      if (!$tel)
        return $this->sendError(null, ['No existe el telefono']);

      return $tel->update($validator) ? $this->sendResponse($tel) : $this->sendError($tel->errors, ['No se pudo actualizar']);
    }
  }
}

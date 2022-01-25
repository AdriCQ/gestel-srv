<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  /**
   * Auth Login
   * @param Request request
   * @return Illuminate\Http\JsonResponse
   */
  public function authLogin(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'nick' => ['required', 'string'],
      'password' => ['required', 'string'],
      'remember' => ['nullable', 'boolean'],
      'service_name' => ['required', 'string']
    ]);
    if ($validator->fails()) {
      return $this->sendError($validator->errors()->toArray(), ['Parámetros no válidos']);
    } else {
      $validator = $validator->validate();
      if (Auth::attempt([
        'nick' => $validator['nick'],
        'password' => $validator['password']
      ], $validator['remember'])) {
        $user = User::query()->find(auth()->id());
        $token = $user->createToken($validator['service_name'])->plainTextToken;
        return $this->sendResponse([
          'profile' => $user,
          'api_token' => $token,
          'roles' => $user->roles
        ], ['Bienvenido ' . $user->nombre]);
      }
      return $this->sendError(null, ['Error de credenciales']);
    }
  }
}

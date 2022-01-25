<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public function sendResponse($data, $message = [])
  {
    $response = [
      'success' => true,
      'data'    => $data,
      'message' => $message,
    ];
    return response()->json($response, 200);
  }

  /**
   * return error response.
   *
   * @return \Illuminate\Http\Response
   */
  public function sendError($error, $errorMessages = [], $code = 400)
  {
    $response = [
      'success' => false,
      'data' => $error,
      'message' => $errorMessages
    ];
    if ($code === 401) {
      $response['message'] = ['Autenticación Requerida'];
    }
    return response()->json($response, $code);
  }
}

<?php

namespace App\Helpers;

use Dotenv\Exception\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Google\Cloud\Core\Exception\ConflictException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResponseHelper
{
    public function error($e, $input = null)
    {
        if ($e instanceof AuthorizationException || $e instanceof JWTException) {
            return $this->responseError($e->getMessage() ?? 'Unauthorize', 401,$input, $e->getTrace());
        } elseif ($e instanceof ValidationException) {
            return $this->responseError($e->getMessage() ?? 'Unprocessable Content', 422,$input, $e->getTrace());
        } elseif ($e instanceof ModelNotFoundException) {
            return $this->responseError($e->getMessage() ?? 'Data not found', 404,$input, $e->getTrace());
        } elseif ($e instanceof ConflictException) {
            return $this->responseError($e->getMessage() ?? 'Conflict', 409,$input, $e->getTrace());
        } else {
            return $this->responseError($e->getMessage() ?? 'Internal Server Error', 500,$input, $e->getTrace());
        }
    }

    private function responseError($message, $statusCode, $input, $trace) {
        $response = [
            'status' => 'error',
            'message' => $message,
            'status_code' => $statusCode,
            'trace' => $trace
        ];

        if ($input) {
            $response['input'] = $input;
        }

        return response()->json($response);
    }

    public function success($data = null) {
        $response = ['status' => 'success'];
        if ($data !== null) {
            $response['data'] = $data;
        }
        $response['status_code'] = 200;
        
        return response()->json($response);
    }
}
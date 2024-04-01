<?php

namespace App\Helpers;

use Dotenv\Exception\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Google\Cloud\Core\Exception\ConflictException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResponseHelper
{
    public function error($e, $input = null)
    {
        $exceptions = [
            AuthorizationException::class   => ['Unauthorize', 401],
            JWTException::class             => ['Unauthorize', 401],
            AuthenticationException::class  => ['Authentication Failed', 401],
            ValidationException::class      => ['Unprocessable Content', 422],
            ModelNotFoundException::class   => ['Data not found', 404],
            ConflictException::class        => ['Conflict', 409],
            NotFoundHttpException::class    => ['Not Found', 404],
        ];

        [$defaultMessage, $statusCode] = $exceptions[get_class($e)] ?? ['Internal Server Error', 500];

        $message = $e->getMessage() ?: $defaultMessage;

        return $this->responseError($message, $statusCode, $input, $e->getTrace());
    }

    private function responseError($message, $statusCode, $input, $trace) {
        $response = [
            'status'        => 'error',
            'message'       => $message,
            'status_code'   => $statusCode,
            // 'trace'         => $trace    //debug only
        ];

        if ($input) {
            $response['input'] = $input;
        }

        return response()->json($response);
    }

    public function success($data = null) {
        $response = [
            'status'        => 'success',
            'status_code'   => 200
        ];

        if ($data) {
            $response['data'] = $data;
        }
        
        return response()->json($response);
    }
}
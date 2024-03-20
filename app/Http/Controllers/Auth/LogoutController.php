<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogoutController extends Controller
{
    public function __construct(private ResponseHelper $response) { }
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        try {
            auth()->guard('api')->logout();
            return $this->response->success();
        } catch (JWTException $e) {
            return $this->response->error($e->getMessage(), 401);
        }
    }
}

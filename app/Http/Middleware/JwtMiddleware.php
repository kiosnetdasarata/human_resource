<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Carbon\Exceptions\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function __construct(private ResponseHelper $response) { }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return $this->response->error($e, $request->input());
        }
        return $next($request);
    }
}

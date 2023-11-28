<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsHumanResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token') ?? $request->query('token');
        if (!$token) {
            return response()->json([
                'error' => 'Token not provded.'
            ], 401);
        }
        $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
        if ($credentials->role !== 'hr') {
            return response()->json([
                'status' => 'error',
                'error' => 'invalid credentials',
                'status_code' => 403
            ]);   
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            if ($token = auth()->guard('api')->attempt($request->safe()->only('karyawan_nip', 'password'))) {
                
                return response()->json([
                    'success' => true,
                    'user'    => auth()->guard('api')->user(),    
                    'token'   => $token   
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'NIP atau Password Anda salah'
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

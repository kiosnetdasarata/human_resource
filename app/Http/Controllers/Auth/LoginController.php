<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            if ($token = auth()->guard('api')->attempt($request->safe()->only('nip_id', 'password'))) {
                return response()->json([
                    'success' => true,
                    'user'    => auth()->guard('api')->user(),    
                    'token'   => $token   
                ], 200);
            }
            throw new Exception('NIP atau Password Anda salah');
            // return response()->json([
            //     'success' => false,
            //     'message' => 'NIP atau Password Anda salah'
            // ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

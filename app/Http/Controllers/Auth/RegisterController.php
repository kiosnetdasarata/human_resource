<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'karyawan_nip' => $request['karyawan_nip'],
                'is_leader' => $request['is_leader'],
                'password' => Hash::make($request['password']),
            ]);

            if ($token = auth()->guard('api')->login($user)) {
                return response()->json([
                    'success' => true,
                    'user'    => auth()->guard('api')->user(),    
                    'token'   => $token   
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'login error',
            ], 401);
            
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

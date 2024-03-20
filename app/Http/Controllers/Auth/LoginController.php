<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Interfaces\UserRepositoryInterface;

class LoginController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepositoryInterface)
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            if ($token = JWTAuth::attempt($request->safe()->only('nip_id', 'password'))) {
                
                // $this->userRepositoryInterface->update($user, ['remember_token' => $token]);
                return response()->json([
                    'success' => true,
                    'user'    => auth()->guard('api')->user(),
                    'token'   => $token
                ], 200);
            }
            throw new Exception('NIP atau Password Anda salah');

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

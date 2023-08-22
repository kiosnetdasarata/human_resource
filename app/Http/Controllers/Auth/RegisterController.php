<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\UserRepositoryInterface;

class RegisterController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepositoryInterface)
    {
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        try {
            $user = $this->userRepositoryInterface->create($request->validated());

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

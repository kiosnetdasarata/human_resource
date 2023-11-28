<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
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
            if (auth()->guard('api')->attempt($request->safe()->only('nip_id', 'password'))) {
                $user = auth()->guard('api')->user();
                $token = $this->jwt($user);
                $this->userRepositoryInterface->update($user, ['remember_token' => $token]);
                return response()->json([
                    'success' => true,
                    'user'    => auth()->guard('api')->user(),
                    'token'   => $token
                ], 200);
            }
            throw new Exception('NIP atau Password Anda salah');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    protected function jwt(User $user)
    {
        $payload = [
            'iss' => 'dasatara', //issuer of the token
            'sub' => $user->nip_id, //subject of the token
            'iat' => time(), //time when JWT was issued.
            'exp' => time() + 60 * 60, //time when JWT will expire
            'role' => $user->employee->role->nama_jabatan
        ];
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }
    
    public function login(LoginRequest $request)
    {
        $nip = $request->nip;
        $password = $request->password;
        $user = User::where('nip', $nip)->first();
    
        if (!$user) {
            return response()->json([
                'status' => 'Error',
                'message' => 'user not exist',
            ], 404);
        }
    
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'wrong password',
            ], 400);
        }
    
        $user->token = $this->jwt($user); //
        $user->save();
        
        return response()->json([
            'status' => 'Success',
            'message' => 'successfully login',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }
}

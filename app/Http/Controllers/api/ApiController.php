<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{
    // Registro de usuarios
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    // Login de usuario
    public function login(LoginRequest $request)
    {
        if (!$token = auth('api')->attempt($request->only('email', 'password'))) {
            return $this->errorResponse("Invalid Login credentials", 401);
        }

        return $this->respondWithToken($token);
    }

    // Logout (invalidar el token)
    public function logout()
    {
        try {
            auth('api')->logout();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

    // Refrescar el token JWT
    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh();
            auth('api')->invalidate(true); // Invalida el token antiguo
            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }
    }

    // Obtener perfil del usuario autenticado
    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    // Responder con el token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 // Asegúrate de usar el guard correcto
        ]);
    }
}

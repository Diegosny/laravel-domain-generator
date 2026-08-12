<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Abstracts\AbstractController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends AbstractController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return $this->error('Credenciais inválidas', [], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return $this->ok(Auth::guard('api')->user());
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return $this->success('Logout realizado com sucesso');
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return $this->ok([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
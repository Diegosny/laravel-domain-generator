<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Abstracts\AbstractController;
use Domain\DomainGenerator\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends AbstractController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = auth('api')->attempt($credentials)) {
            return $this->error(
                'Credenciais inválidas.',
                401
            );
        }

        return $this->success(
            $this->tokenPayload($token)
        );
    }

    public function me(): JsonResponse
    {
        return $this->success(
            auth('api')->user()
        );
    }

    public function refresh(): JsonResponse
    {
        return $this->success(
            $this->tokenPayload(
                auth('api')->refresh()
            )
        );
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return $this->success([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    protected function tokenPayload(string $token): array
    {
        $guard = auth('api');

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ];
    }
}
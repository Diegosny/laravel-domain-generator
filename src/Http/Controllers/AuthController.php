<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Abstracts\AbstractController;
use Domain\DomainGenerator\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends AbstractController
{
    /**
     * Realiza autenticação do usuário.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return $this->error(
                'Credenciais inválidas.',
                401
            );
        }

        return $this->success(
            $this->tokenPayload($token)
        );
    }

    /**
     * Retorna o usuário autenticado.
     */
    public function me(): JsonResponse
    {
        return $this->success(
            Auth::guard('api')->user()
        );
    }

    /**
     * Renova o token JWT.
     */
    public function refresh(): JsonResponse
    {
        return $this->success(
            $this->tokenPayload(
                Auth::guard('api')->refresh()
            )
        );
    }

    /**
     * Realiza logout.
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return $this->success([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }

    /**
     * Monta o payload padrão do JWT.
     */
    protected function tokenPayload(string $token): array
    {
        $guard = Auth::guard('api');

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ];
    }
}
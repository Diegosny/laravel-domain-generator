<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $field = config('domain-generator.auth.login_field', 'email');

        $credentials = [
            $field => $request->validated()[$field],
            'password' => $request->validated()['password'],
        ];

        if (! $token = auth(config('domain-generator.auth.guard', 'api'))->attempt($credentials)) {
            return response()->json([
                'type' => 'error',
                'status' => 401,
                'message' => 'Credenciais inválidas.',
                'show' => app()->hasDebugModeEnabled(),
            ], 401);
        }

        return response()->json([
            'type' => 'success',
            'status' => 200,
            'data' => $this->tokenPayload($token),
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'type' => 'success',
            'status' => 200,
            'data' => auth(config('domain-generator.auth.guard', 'api'))->user(),
        ]);
    }

    public function refresh(): JsonResponse
    {
        return response()->json([
            'type' => 'success',
            'status' => 200,
            'data' => $this->tokenPayload(
                auth(config('domain-generator.auth.guard', 'api'))->refresh()
            ),
        ]);
    }

    public function logout(): JsonResponse
    {
        auth(config('domain-generator.auth.guard', 'api'))->logout();

        return response()->json([
            'type' => 'success',
            'status' => 200,
            'data' => [
                'message' => 'Logout realizado com sucesso.',
            ],
        ]);
    }

    protected function tokenPayload(string $token): array
    {
        $guard = auth(config('domain-generator.auth.guard', 'api'));

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ];
    }
}
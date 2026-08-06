<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public const TYPE_SUCCESS = 'success';
    public const TYPE_ERROR = 'error';

    private const KEY_TYPE = 'type';
    private const KEY_STATUS = 'status';
    private const KEY_DATA = 'data';
    private const KEY_MESSAGE = 'message';
    private const KEY_SHOW = 'show';
    private const KEY_ERRORS = 'errors';

    private const UNAUTHORIZED_MESSAGE = 'Você não tem permissão suficiente para executar essa ação';

    protected string $messageSuccessDefault = 'Operação realizada com sucesso';
    protected string $messageErrorDefault = 'Ops';

    public function ok(
        mixed $items = [],
        int   $status = Response::HTTP_OK
    ): JsonResponse
    {
        return $this->jsonResponse([
            self::KEY_TYPE => self::TYPE_SUCCESS,
            self::KEY_STATUS => $status,
            self::KEY_DATA => $items,
            self::KEY_SHOW => false,
        ], $status);
    }

    public function error(
        string $message = '',
        array  $items = [],
        int    $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse
    {
        $payload = [
            self::KEY_TYPE => self::TYPE_ERROR,
            self::KEY_STATUS => $status,
            self::KEY_MESSAGE => $this->resolveMessage($message, $this->messageErrorDefault),
            self::KEY_SHOW => true,
        ];

        if (!empty($items)) {
            $payload[self::KEY_ERRORS] = $items;
        }

        return $this->jsonResponse($payload, $status);
    }

    public function success(
        string $message = '',
        mixed  $items = [],
        int    $status = Response::HTTP_OK
    ): JsonResponse
    {
        $payload = [
            self::KEY_TYPE => self::TYPE_SUCCESS,
            self::KEY_STATUS => $status,
            self::KEY_MESSAGE => $this->resolveMessage($message, $this->messageSuccessDefault),
            self::KEY_SHOW => true,
        ];

        return $this->jsonResponse(
            array_merge($payload, $this->toArrayPayload($items)),
            $status
        );
    }

    public function getUserAuth(): mixed
    {
        return Auth::user();
    }

    public function hasPermissionTo(string $permission): void
    {
        $user = $this->getUserAuth();

        if (!$user || !$user->hasPermissionTo($permission)) {
            throw new UnauthorizedException(
                Response::HTTP_FORBIDDEN,
                self::UNAUTHORIZED_MESSAGE
            );
        }
    }

    protected function jsonResponse(array $payload, int $status): JsonResponse
    {
        return response()->json($payload, $status);
    }

    protected function resolveMessage(string $message, string $defaultMessage): string
    {
        return filled($message) ? $message : $defaultMessage;
    }

    protected function toArrayPayload(mixed $payload): array
    {
        if ($payload instanceof Arrayable) {
            return $payload->toArray();
        }

        return is_array($payload) ? $payload : [];
    }
}
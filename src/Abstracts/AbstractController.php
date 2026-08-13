<?php

namespace Domain\DomainGenerator\Abstracts;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class AbstractController extends BaseController
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

    protected array $with = [];

    protected mixed $service;

    protected ?string $resource = null;

    protected ?string $requestValidate = null;

    protected ?string $requestValidateUpdate = null;

    protected string $messageSuccessDefault = 'Operação realizada com sucesso';

    protected string $messageErrorDefault = 'Ops';

    public function index(Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->getAll($request->all(), $this->resolveWith($request))
        );
    }

    public function store(): JsonResponse
    {
        return $this->handle(function () {
            $validated = $this->validateStoreRequest();

            $response = DB::transaction(fn () => $this->service->save($validated));

            return ['response' => $response];
        }, successMessage: $this->messageSuccessDefault);
    }

    public function update(Request $request, mixed $id): JsonResponse
    {
        return $this->handle(function () use ($request, $id) {
            $validated = $this->validateUpdateRequest($request);

            DB::transaction(fn () => $this->service->update($id, $validated));

            return [];
        }, successMessage: $this->messageSuccessDefault);
    }

    public function show(mixed $id, Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->find($id, $this->resolveWith($request))
        );
    }

    public function destroy(mixed $id): JsonResponse
    {
        return $this->handle(function () use ($id) {
            DB::transaction(fn () => $this->service->delete($id));

            return [];
        }, successMessage: $this->messageSuccessDefault);
    }

    public function preRequisite(mixed $id = null): JsonResponse
    {
        return $this->handle(
            fn () => ['preRequisite' => $this->service->preRequisite($id)]
        );
    }

    public function toSelect(): JsonResponse
    {
        return $this->handle(fn () => $this->service->toSelect());
    }

    protected function handle(Closure $callback, ?string $successMessage = null): JsonResponse
    {
        try {
            $result = $callback();

            return $successMessage !== null
                ? $this->success($successMessage, $result)
                : $this->ok($result);
        } catch (ValidationException $exception) {
            report($exception);

            return $this->error($this->messageErrorDefault, $exception->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $exception) {
            report($exception);

            return $this->error($exception->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function resolveWith(Request $request): array|string
    {
        return $request->input('with', $this->with);
    }

    protected function validateStoreRequest(): array
    {
        if ($this->requestValidate === null) {
            return [];
        }

        return app($this->requestValidate)->validated();
    }

    protected function validateUpdateRequest(Request $request): array
    {
        if ($this->requestValidateUpdate !== null) {
            return app($this->requestValidateUpdate)->validated();
        }

        if ($this->requestValidate !== null) {
            return app($this->requestValidate)->validated();
        }

        return $request->all();
    }

    // --- MÉTODOS DE FORMATAÇÃO DE RESPOSTA ---

    public function ok(
        mixed $items = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE => self::TYPE_SUCCESS,
            self::KEY_STATUS => $status,
            self::KEY_SHOW => false,
        ];

        return $this->jsonResponse(
            array_merge($payload, $this->toArrayPayload($items)),
            $status
        );
    }

    public function error(
        string $message = '',
        array $items = [],
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
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
        mixed $items = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
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
        if (empty($payload)) {
            return [];
        }

        // 1. Se um Resource foi definido no Controller
        if ($this->resource !== null && class_exists($this->resource)) {
            $resourceClass = $this->resource;

            if ($payload instanceof LengthAwarePaginator) {
                return $resourceClass::collection($payload)->response()->getData(true);
            }

            if ($payload instanceof Collection) {
                return [self::KEY_DATA => $resourceClass::collection($payload)->resolve()];
            }

            return [self::KEY_DATA => (new $resourceClass($payload))->resolve()];
        }

        // 2. Se for um Paginator nativo do Eloquent
        if ($payload instanceof LengthAwarePaginator) {
            return $payload->toArray();
        }

        // 3. Se for um Array contendo chaves de paginação (fallback caso já venha formatado)
        if (is_array($payload) && isset($payload['data']) && (isset($payload['current_page']) || isset($payload['total']))) {
            return $payload;
        }

        // 4. Se for Arrayable (Model, Collection, etc.)
        if ($payload instanceof Arrayable) {
            return [self::KEY_DATA => $payload->toArray()];
        }

        // 5. Se já for um array associativo contendo a chave 'data'
        if (is_array($payload) && array_key_exists(self::KEY_DATA, $payload)) {
            return $payload;
        }

        return [self::KEY_DATA => is_array($payload) ? $payload : []];
    }
}

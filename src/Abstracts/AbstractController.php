<?php

namespace Domain\DomainGenerator\Abstracts;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

abstract class AbstractController extends Controller
{
    protected mixed $service;

    protected ?string $requestValidate = null;

    protected ?string $requestValidateUpdate = null;

    protected ?string $requestDto = null;

    protected ?string $requestDtoUpdate = null;

    protected ?string $resource = null;

    protected array $with = [];

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->getAll(
                $request->query(),
                $this->resolveWith($request)
            )
        );
    }

    public function show(Request $request, mixed $id): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->find(
                $id,
                $this->resolveWith($request)
            )
        );
    }

    public function store(Request $request): JsonResponse
    {
        return $this->handle(function () use ($request) {

            $data = $this->validatedData(
                $request,
                false
            );

            if ($this->requestDto) {
                return $this->service->saveDto(
                    $this->requestDto::fromArray($data)
                );
            }

            return $this->service->save($data);
        }, 201);
    }

    public function update(Request $request, mixed $id): JsonResponse
    {
        return $this->handle(function () use ($request, $id) {

            $data = $this->validatedData(
                $request,
                true
            );

            if ($this->requestDtoUpdate) {
                return $this->service->updateDto(
                    $id,
                    $this->requestDtoUpdate::fromArray($data)
                );
            }

            return $this->service->update(
                $id,
                $data
            );
        });
    }

    public function destroy(mixed $id): JsonResponse
    {
        return $this->handle(function () use ($id) {

            $this->service->delete($id);

            return [
                'message' => 'Registro removido com sucesso.'
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Handler
    |--------------------------------------------------------------------------
    */

    protected function handle(
        callable $callback,
        int $status = 200
    ): JsonResponse {

        try {

            $result = $callback();

            return $this->success(
                $this->applyResource($result),
                $status
            );

        } catch (ValidationException $e) {

            return $this->error(
                $e->errors(),
                422
            );

        } catch (AuthorizationException $e) {

            return $this->error(
                $e->getMessage(),
                403
            );

        } catch (Throwable $e) {

            return $this->error(
                app()->hasDebugModeEnabled()
                    ? $e->getMessage()
                    : 'Erro interno do servidor.',
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    */

    protected function applyResource(mixed $payload): mixed
    {
        if (! $this->resource) {
            return $payload;
        }

        if ($payload === null) {
            return null;
        }

        if ($payload instanceof JsonResource) {
            return $payload;
        }

        $resource = $this->resource;

        if ($payload instanceof LengthAwarePaginator) {
            return $resource::collection($payload);
        }

        if ($payload instanceof EloquentCollection) {
            return $resource::collection($payload);
        }

        if ($payload instanceof Collection) {
            return $resource::collection($payload);
        }

        if ($payload instanceof Model) {
            return new $resource($payload);
        }

        return $payload;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function resolveWith(Request $request): array
    {
        $queryWith = $request->query('with');

        if (! $queryWith) {
            return $this->with;
        }

        return array_values(
            array_unique(
                array_merge(
                    $this->with,
                    array_filter(
                        array_map(
                            'trim',
                            explode(',', $queryWith)
                        )
                    )
                )
            )
        );
    }

    protected function validatedData(
        Request $request,
        bool $update = false
    ): array {

        $requestClass = $update
            ? $this->requestValidateUpdate
            : $this->requestValidate;

        if (! $requestClass) {
            return $request->all();
        }

        /** @var FormRequest $formRequest */
        $formRequest = app($requestClass);

        $formRequest->setContainer(app())
            ->setRedirector(app('redirect'))
            ->initialize(
                $request->query->all(),
                $request->request->all(),
                [],
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent()
            );

        $formRequest->validateResolved();

        return $formRequest->validated();
    }

    protected function hasPermissionTo(
        string $permission
    ): void {

        $user = Auth::user();

        if (! $user || ! method_exists($user, 'hasPermissionTo')) {
            throw new AuthorizationException(
                'Usuário não autenticado.'
            );
        }

        if (! $user->hasPermissionTo($permission)) {
            throw new AuthorizationException(
                'Você não possui permissão para executar esta ação.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Responses
    |--------------------------------------------------------------------------
    */

    protected function success(
        mixed $data = null,
        int $status = 200
    ): JsonResponse {

        return response()->json([
            'type' => 'success',
            'status' => $status,
            'data' => $data,
        ], $status);
    }

    protected function error(
        mixed $message,
        int $status = 500
    ): JsonResponse {

        return response()->json([
            'type' => 'error',
            'status' => $status,
            'message' => $message,
            'show' => app()->hasDebugModeEnabled(),
        ], $status);
    }

    protected function ok(
        mixed $data = null
    ): JsonResponse {

        return $this->success($data);
    }
}
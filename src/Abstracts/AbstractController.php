<?php

namespace App\Abstracts;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

abstract class AbstractController extends Controller
{
    protected array $with = [];

    protected mixed $service;

    protected ?string $requestValidate = null;

    protected ?string $requestValidateUpdate = null;

    public function index(Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service
                ->getAll($request->all(), $this->resolveWith($request))
                ->toArray()
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
            $validated = $this->validateUpdateRequest();

            DB::transaction(fn () => $this->service->update($id, $validated));

            return [];
        }, successMessage: $this->messageSuccessDefault);
    }

    public function show(mixed $id, Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->find($id, $this->resolveWith($request))->toArray()
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

            return $this->error($this->messageErrorDefault, $exception->errors());
        } catch (Throwable $exception) {
            report($exception);

            return $this->error($exception->getMessage());
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

    protected function validateUpdateRequest(): array
    {
        if ($this->requestValidateUpdate === null) {
            return [];
        }

        return app($this->requestValidateUpdate)->validated();
    }
}
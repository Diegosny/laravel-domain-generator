<?php

namespace Domain\DomainGenerator\Abstracts;

use Closure;
use Domain\DomainGenerator\Interfaces\DTOInterface;
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
use InvalidArgumentException;
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

    private const UNAUTHORIZED_MESSAGE =
        'Você não tem permissão suficiente para executar essa ação';

    /**
     * Default relationships used by index/show queries.
     */
    protected array $with = [];

    /**
     * Service used by the controller.
     */
    protected mixed $service;

    /**
     * Optional API Resource class.
     */
    protected ?string $resource = null;

    /**
     * FormRequest class used for store.
     */
    protected ?string $requestValidate = null;

    /**
     * FormRequest class used for update.
     */
    protected ?string $requestValidateUpdate = null;

    /**
     * DTO class used for store.
     *
     * Example:
     *
     * protected ?string $requestDto = UserStoreDTO::class;
     */
    protected ?string $requestDto = null;

    /**
     * DTO class used for update.
     *
     * If null, $requestDto will be used.
     */
    protected ?string $requestDtoUpdate = null;

    /**
     * Default success message.
     */
    protected string $messageSuccessDefault =
        'Operação realizada com sucesso';

    /**
     * Default error message.
     */
    protected string $messageErrorDefault = 'Ops';

    /**
     * List resources.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->getAll(
                $request->all(),
                $this->resolveWith($request)
            )
        );
    }

    /**
     * Store resource.
     */
    public function store(): JsonResponse
    {
        return $this->handle(
            function () {
                $validated = $this->validateStoreRequest();

                $response = DB::transaction(
                    fn () => $this->saveToService($validated)
                );

                return ['response' => $response];
            },
            successMessage: $this->messageSuccessDefault
        );
    }

    /**
     * Update resource.
     */
    public function update(
        Request $request,
        mixed $id
    ): JsonResponse {
        return $this->handle(
            function () use ($request, $id) {
                $validated = $this->validateUpdateRequest($request);

                DB::transaction(
                    fn () => $this->updateToService(
                        $id,
                        $validated
                    )
                );

                return [];
            },
            successMessage: $this->messageSuccessDefault
        );
    }

    /**
     * Show resource.
     */
    public function show(
        mixed $id,
        Request $request
    ): JsonResponse {
        return $this->handle(
            fn () => $this->service->find(
                $id,
                $this->resolveWith($request)
            )
        );
    }

    /**
     * Delete resource.
     */
    public function destroy(mixed $id): JsonResponse
    {
        return $this->handle(
            function () use ($id) {
                DB::transaction(
                    fn () => $this->service->delete($id)
                );

                return [];
            },
            successMessage: $this->messageSuccessDefault
        );
    }

    /**
     * Return prerequisites.
     */
    public function preRequisite(
        mixed $id = null
    ): JsonResponse {
        return $this->handle(
            fn () => [
                'preRequisite' => $this->service->preRequisite($id),
            ]
        );
    }

    /**
     * Return select options.
     */
    public function toSelect(): JsonResponse
    {
        return $this->handle(
            fn () => $this->service->toSelect()
        );
    }

    /**
     * Handle controller execution.
     */
    protected function handle(
        Closure $callback,
        ?string $successMessage = null
    ): JsonResponse {
        try {
            $result = $callback();

            return $successMessage !== null
                ? $this->success($successMessage, $result)
                : $this->ok($result);
        } catch (ValidationException $exception) {
            report($exception);

            return $this->error(
                $this->messageErrorDefault,
                $exception->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->error(
                $exception->getMessage(),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Resolve relationships from request.
     */
    protected function resolveWith(
        Request $request
    ): array|string {
        return $request->input(
            'with',
            $this->with
        );
    }

    /**
     * Validate store request and optionally convert it to DTO.
     */
    protected function validateStoreRequest(): array|DTOInterface
    {
        if ($this->requestValidate === null) {
            return [];
        }

        $validated = app(
            $this->requestValidate
        )->validated();

        return $this->makeDto(
            $validated,
            $this->requestDto
        );
    }

    /**
     * Validate update request and optionally convert it to DTO.
     */
    protected function validateUpdateRequest(
        Request $request
    ): array|DTOInterface {
        if ($this->requestValidateUpdate !== null) {
            $validated = app(
                $this->requestValidateUpdate
            )->validated();
        } elseif ($this->requestValidate !== null) {
            $validated = app(
                $this->requestValidate
            )->validated();
        } else {
            $validated = $request->all();
        }

        return $this->makeDto(
            $validated,
            $this->requestDtoUpdate ?? $this->requestDto
        );
    }

    /**
     * Convert validated data into a DTO when configured.
     *
     * If no DTO is configured, the original array is returned.
     */
    protected function makeDto(
        array $data,
        ?string $dtoClass
    ): array|DTOInterface {
        if ($dtoClass === null) {
            return $data;
        }

        if (! class_exists($dtoClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    'DTO class [%s] does not exist.',
                    $dtoClass
                )
            );
        }

        if (! is_a(
            $dtoClass,
            DTOInterface::class,
            true
        )) {
            throw new InvalidArgumentException(
                sprintf(
                    'DTO [%s] must implement [%s].',
                    $dtoClass,
                    DTOInterface::class
                )
            );
        }

        return $dtoClass::fromArray($data);
    }

    /**
     * Save data through the configured service.
     *
     * DTOs are handled through saveDto().
     * Arrays continue using the original save() method.
     */
    protected function saveToService(
        array|DTOInterface $data
    ): mixed {
        if ($data instanceof DTOInterface) {
            if (! method_exists(
                $this->service,
                'saveDto'
            )) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service [%s] does not support DTOs. ' .
                        'Implement saveDto() in the service.',
                        get_class($this->service)
                    )
                );
            }

            return $this->service->saveDto($data);
        }

        return $this->service->save($data);
    }

    /**
     * Update data through the configured service.
     *
     * DTOs are handled through updateDto().
     * Arrays continue using the original update() method.
     */
    protected function updateToService(
        mixed $id,
        array|DTOInterface $data
    ): mixed {
        if ($data instanceof DTOInterface) {
            if (! method_exists(
                $this->service,
                'updateDto'
            )) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service [%s] does not support DTOs. ' .
                        'Implement updateDto() in the service.',
                        get_class($this->service)
                    )
                );
            }

            return $this->service->updateDto(
                $id,
                $data
            );
        }

        return $this->service->update(
            $id,
            $data
        );
    }

    // ---------------------------------------------------------
    // Response formatting
    // ---------------------------------------------------------

    /**
     * Return a successful response.
     */
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
            array_merge(
                $payload,
                $this->toArrayPayload($items)
            ),
            $status
        );
    }

    /**
     * Return an error response.
     */
    public function error(
        string $message = '',
        array $items = [],
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE => self::TYPE_ERROR,
            self::KEY_STATUS => $status,
            self::KEY_MESSAGE => $this->resolveMessage(
                $message,
                $this->messageErrorDefault
            ),
            self::KEY_SHOW => true,
        ];

        if (! empty($items)) {
            $payload[self::KEY_ERRORS] = $items;
        }

        return $this->jsonResponse(
            $payload,
            $status
        );
    }

    /**
     * Return a successful response with message.
     */
    public function success(
        string $message = '',
        mixed $items = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE => self::TYPE_SUCCESS,
            self::KEY_STATUS => $status,
            self::KEY_MESSAGE => $this->resolveMessage(
                $message,
                $this->messageSuccessDefault
            ),
            self::KEY_SHOW => true,
        ];

        return $this->jsonResponse(
            array_merge(
                $payload,
                $this->toArrayPayload($items)
            ),
            $status
        );
    }

    /**
     * Get authenticated user.
     */
    public function getUserAuth(): mixed
    {
        return Auth::user();
    }

    /**
     * Check user permission.
     */
    public function hasPermissionTo(
        string $permission
    ): void {
        $user = $this->getUserAuth();

        if (
            ! $user ||
            ! $user->hasPermissionTo($permission)
        ) {
            throw new UnauthorizedException(
                Response::HTTP_FORBIDDEN,
                self::UNAUTHORIZED_MESSAGE
            );
        }
    }

    /**
     * Create JSON response.
     */
    protected function jsonResponse(
        array $payload,
        int $status
    ): JsonResponse {
        return response()->json(
            $payload,
            $status
        );
    }

    /**
     * Resolve response message.
     */
    protected function resolveMessage(
        string $message,
        string $defaultMessage
    ): string {
        return filled($message)
            ? $message
            : $defaultMessage;
    }

    /**
     * Convert response payload into the expected API structure.
     */
    protected function toArrayPayload(
        mixed $payload
    ): array {
        if (empty($payload)) {
            return [];
        }

        /*
         * 1. Resource configured on Controller.
         */
        if (
            $this->resource !== null &&
            class_exists($this->resource)
        ) {
            $resourceClass = $this->resource;

            if ($payload instanceof LengthAwarePaginator) {
                return $resourceClass::collection(
                    $payload
                )->response()->getData(true);
            }

            if ($payload instanceof Collection) {
                return [
                    self::KEY_DATA =>
                        $resourceClass::collection(
                            $payload
                        )->resolve(),
                ];
            }

            return [
                self::KEY_DATA =>
                    (new $resourceClass(
                        $payload
                    ))->resolve(),
            ];
        }

        /*
         * 2. Native Eloquent paginator.
         */
        if ($payload instanceof LengthAwarePaginator) {
            return $payload->toArray();
        }

        /*
         * 3. Already formatted pagination array.
         */
        if (
            is_array($payload) &&
            isset($payload[self::KEY_DATA]) &&
            (
                isset($payload['current_page']) ||
                isset($payload['total'])
            )
        ) {
            return $payload;
        }

        /*
         * 4. Arrayable object.
         *
         * This includes DTOs, Models and Collections.
         */
        if ($payload instanceof Arrayable) {
            return [
                self::KEY_DATA => $payload->toArray(),
            ];
        }

        /*
         * 5. Already formatted data payload.
         */
        if (
            is_array($payload) &&
            array_key_exists(
                self::KEY_DATA,
                $payload
            )
        ) {
            return $payload;
        }

        /*
         * 6. Default response.
         */
        return [
            self::KEY_DATA => is_array($payload)
                ? $payload
                : [],
        ];
    }
}
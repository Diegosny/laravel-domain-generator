<?php

namespace Domain\DomainGenerator\Abstracts;

use Closure;
use Domain\DomainGenerator\Interfaces\DTOInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Default relationships.
     */
    protected array $with = [];

    /**
     * Service used by Controller.
     */
    protected mixed $service;

    /**
     * Optional API Resource.
     */
    protected ?string $resource = null;

    /**
     * FormRequest used for store.
     */
    protected ?string $requestValidate = null;

    /**
     * FormRequest used for update.
     */
    protected ?string $requestValidateUpdate = null;

    /**
     * DTO used for store.
     */
    protected ?string $requestDto = null;

    /**
     * DTO used for update.
     *
     * When null, requestDto is used.
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

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    /**
     * List resources.
     */
    public function index(
        Request $request
    ): JsonResponse {
        return $this->handle(
            fn () =>
                $this->service->getAll(
                    $request->query(),
                    $this->resolveWith(
                        $request
                    )
                )
        );
    }

    /**
     * Store resource.
     */
    public function store(
        Request $request
    ): JsonResponse {
        return $this->handle(
            function () use ($request) {
                $validated =
                    $this->validateStoreRequest(
                        $request
                    );

                return DB::transaction(
                    fn () =>
                        $this->saveToService(
                            $validated
                        )
                );
            },
            successMessage:
                $this->messageSuccessDefault
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
            function () use (
                $request,
                $id
            ) {
                $validated =
                    $this->validateUpdateRequest(
                        $request
                    );

                return DB::transaction(
                    fn () =>
                        $this->updateToService(
                            $id,
                            $validated
                        )
                );
            },
            successMessage:
                $this->messageSuccessDefault
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
            fn () =>
                $this->service->find(
                    $id,
                    $this->resolveWith(
                        $request
                    )
                )
        );
    }

    /**
     * Delete resource.
     */
    public function destroy(
        mixed $id
    ): JsonResponse {
        return $this->handle(
            function () use ($id) {
                DB::transaction(
                    fn () =>
                        $this->service->delete(
                            $id
                        )
                );

                return [];
            },
            successMessage:
                $this->messageSuccessDefault
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Extra endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * Return prerequisites.
     */
    public function preRequisite(
        mixed $id = null
    ): JsonResponse {
        return $this->handle(
            fn () => [
                'preRequisite' =>
                    $this->service
                        ->preRequisite($id),
            ]
        );
    }

    /**
     * Return select options.
     */
    public function toSelect(): JsonResponse
    {
        return $this->handle(
            fn () =>
                $this->service->toSelect()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Execution
    |--------------------------------------------------------------------------
    */

    /**
     * Execute Controller action and normalize exceptions.
     */
    protected function handle(
        Closure $callback,
        ?string $successMessage = null
    ): JsonResponse {
        try {
            $result = $callback();

            return $successMessage !== null
                ? $this->success(
                    $successMessage,
                    $result
                )
                : $this->ok($result);
        } catch (
            ValidationException $exception
        ) {
            return $this->error(
                $this->messageErrorDefault,
                $exception->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (
            AuthorizationException $exception
        ) {
            return $this->error(
                $exception->getMessage(),
                [],
                Response::HTTP_FORBIDDEN
            );
        } catch (
            ModelNotFoundException $exception
        ) {
            return $this->error(
                'Registro não encontrado',
                [],
                Response::HTTP_NOT_FOUND
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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve relationships from query string.
     *
     * Supports:
     *
     * ?with=roles,permissions
     *
     * ?with[]=roles&with[]=permissions
     */
    protected function resolveWith(
        Request $request
    ): array|string|null {
        return $request->query(
            'with',
            $this->with
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation / DTO
    |--------------------------------------------------------------------------
    */

    /**
     * Validate store request and optionally
     * convert data into DTO.
     */
    protected function validateStoreRequest(
        Request $request
    ): array|DTOInterface {
        if (
            $this->requestValidate === null
        ) {
            return $this->makeDto(
                $request->all(),
                $this->requestDto
            );
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
     * Validate update request and optionally
     * convert data into DTO.
     */
    protected function validateUpdateRequest(
        Request $request
    ): array|DTOInterface {
        if (
            $this->requestValidateUpdate
                !== null
        ) {
            $validated = app(
                $this->requestValidateUpdate
            )->validated();
        } elseif (
            $this->requestValidate
                !== null
        ) {
            $validated = app(
                $this->requestValidate
            )->validated();
        } else {
            $validated = $request->all();
        }

        return $this->makeDto(
            $validated,
            $this->requestDtoUpdate ??
                $this->requestDto
        );
    }

    /**
     * Convert array into configured DTO.
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

        if (
            ! is_a(
                $dtoClass,
                DTOInterface::class,
                true
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'DTO [%s] must implement [%s].',
                    $dtoClass,
                    DTOInterface::class
                )
            );
        }

        return $dtoClass::fromArray(
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Service delegation
    |--------------------------------------------------------------------------
    */

    /**
     * Save through Service.
     */
    protected function saveToService(
        array|DTOInterface $data
    ): mixed {
        if (
            $data
                instanceof DTOInterface
        ) {
            if (
                ! method_exists(
                    $this->service,
                    'saveDto'
                )
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service [%s] does not support DTOs. ' .
                        'Implement saveDto() in the service.',
                        get_class(
                            $this->service
                        )
                    )
                );
            }

            return $this
                ->service
                ->saveDto($data);
        }

        return $this
            ->service
            ->save($data);
    }

    /**
     * Update through Service.
     */
    protected function updateToService(
        mixed $id,
        array|DTOInterface $data
    ): mixed {
        if (
            $data
                instanceof DTOInterface
        ) {
            if (
                ! method_exists(
                    $this->service,
                    'updateDto'
                )
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Service [%s] does not support DTOs. ' .
                        'Implement updateDto() in the service.',
                        get_class(
                            $this->service
                        )
                    )
                );
            }

            return $this
                ->service
                ->updateDto(
                    $id,
                    $data
                );
        }

        return $this
            ->service
            ->update(
                $id,
                $data
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Responses
    |--------------------------------------------------------------------------
    */

    /**
     * Successful response without message.
     */
    public function ok(
        mixed $items = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE =>
                self::TYPE_SUCCESS,

            self::KEY_STATUS =>
                $status,

            self::KEY_SHOW =>
                false,
        ];

        return $this->jsonResponse(
            array_merge(
                $payload,
                $this->toArrayPayload(
                    $items
                )
            ),
            $status
        );
    }

    /**
     * Error response.
     */
    public function error(
        string $message = '',
        array $items = [],
        int $status =
            Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE =>
                self::TYPE_ERROR,

            self::KEY_STATUS =>
                $status,

            self::KEY_MESSAGE =>
                $this->resolveMessage(
                    $message,
                    $this
                        ->messageErrorDefault
                ),

            self::KEY_SHOW =>
                true,
        ];

        if (! empty($items)) {
            $payload[
                self::KEY_ERRORS
            ] = $items;
        }

        return $this->jsonResponse(
            $payload,
            $status
        );
    }

    /**
     * Successful response with message.
     */
    public function success(
        string $message = '',
        mixed $items = [],
        int $status =
            Response::HTTP_OK
    ): JsonResponse {
        $payload = [
            self::KEY_TYPE =>
                self::TYPE_SUCCESS,

            self::KEY_STATUS =>
                $status,

            self::KEY_MESSAGE =>
                $this->resolveMessage(
                    $message,
                    $this
                        ->messageSuccessDefault
                ),

            self::KEY_SHOW =>
                true,
        ];

        return $this->jsonResponse(
            array_merge(
                $payload,
                $this->toArrayPayload(
                    $items
                )
            ),
            $status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication / Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Return authenticated user.
     */
    public function getUserAuth(): mixed
    {
        return Auth::user();
    }

    /**
     * Verify permission.
     */
    public function hasPermissionTo(
        string $permission
    ): void {
        $user = $this->getUserAuth();

        if (
            ! $user ||
            ! method_exists(
                $user,
                'hasPermissionTo'
            ) ||
            ! $user->hasPermissionTo(
                $permission
            )
        ) {
            throw new AuthorizationException(
                self::UNAUTHORIZED_MESSAGE
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Internal response helpers
    |--------------------------------------------------------------------------
    */

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
     * Convert payload to API response.
     */
    protected function toArrayPayload(
        mixed $payload
    ): array {
        if (
            $payload === null ||
            $payload === [] ||
            $payload === ''
        ) {
            return [];
        }

        /*
         * Resource + paginator.
         */
        if (
            $this->hasResource() &&
            $payload
                instanceof LengthAwarePaginator
        ) {
            $resourceClass =
                $this->resource;

            return $resourceClass::collection(
                $payload
            )
                ->response()
                ->getData(true);
        }

        /*
         * Resource + Collection.
         */
        if (
            $this->hasResource() &&
            $payload instanceof Collection
        ) {
            $resourceClass =
                $this->resource;

            return [
                self::KEY_DATA =>
                    $resourceClass::collection(
                        $payload
                    )->resolve(),
            ];
        }

        /*
         * Resource + Model.
         */
        if (
            $this->hasResource() &&
            $payload instanceof Model
        ) {
            $resourceClass =
                $this->resource;

            return [
                self::KEY_DATA =>
                    (new $resourceClass(
                        $payload
                    ))->resolve(),
            ];
        }

        /*
         * Native paginator without Resource.
         */
        if (
            $payload
                instanceof LengthAwarePaginator
        ) {
            return $payload->toArray();
        }

        /*
         * Already formatted pagination.
         */
        if (
            is_array($payload) &&
            isset(
                $payload[
                    self::KEY_DATA
                ]
            ) &&
            (
                isset(
                    $payload[
                        'current_page'
                    ]
                ) ||
                isset(
                    $payload[
                        'total'
                    ]
                )
            )
        ) {
            return $payload;
        }

        /*
         * Arrayable objects.
         */
        if (
            $payload
                instanceof Arrayable
        ) {
            return [
                self::KEY_DATA =>
                    $payload->toArray(),
            ];
        }

        /*
         * Already formatted data.
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
         * Default.
         */
        return [
            self::KEY_DATA =>
                is_array($payload)
                    ? $payload
                    : $payload,
        ];
    }

    /**
     * Determine whether Controller has
     * a valid Resource configured.
     */
    protected function hasResource(): bool
    {
        return
            $this->resource !== null &&
            class_exists(
                $this->resource
            );
    }
}
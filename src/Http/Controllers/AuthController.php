<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Cached model columns for the current request.
     *
     * @var array<string, array<int, string>>
     */
    private array $columnsCache = [];

    /**
     * Authenticate a user and return a JWT token.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {
        $credentials = $request->validated();

        $guard = Auth::guard('api');

        /**
         * Retrieve the user before generating the token.
         *
         * This allows us to check the optional "active"
         * column when it exists.
         */
        $user = $guard
            ->getProvider()
            ->retrieveByCredentials(
                $credentials
            );

        /**
         * If the model has an "active" column and
         * the user is inactive, authentication is denied.
         *
         * When the column does not exist, the library
         * keeps its original behaviour.
         */
        if (
            $user !== null &&
            ! $this->canAuthenticate($user)
        ) {
            return $this->unauthorized();
        }

        /**
         * Validate credentials and generate token.
         */
        if (
            ! $token = $guard->attempt(
                $credentials
            )
        ) {
            return $this->unauthorized();
        }

        /**
         * User successfully authenticated.
         */
        $authenticatedUser = $guard->user();

        /**
         * Record optional login metadata.
         */
        if ($authenticatedUser !== null) {
            $this->recordLogin(
                $authenticatedUser,
                $request
            );
        }

        return $this->respondWithToken(
            $token
        );
    }

    /**
     * Return the authenticated user.
     */
    public function me(): JsonResponse
    {
        return response()->json(
            Auth::guard('api')->user()
        );
    }

    /**
     * Logout and invalidate current token.
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh JWT token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(
            Auth::guard('api')->refresh()
        );
    }

    /**
     * Build JWT response.
     */
    protected function respondWithToken(
        string $token
    ): JsonResponse {
        $guard = Auth::guard('api');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $guard
                ->factory()
                ->getTTL() * 60,
            'user' => $guard->user(),
        ]);
    }

    /**
     * Determine whether the user is allowed
     * to authenticate.
     *
     * Behaviour:
     *
     * active column does not exist:
     *     true
     *
     * active = true:
     *     true
     *
     * active = false:
     *     false
     */
    protected function canAuthenticate(
        Authenticatable $user
    ): bool {
        if (! $user instanceof Model) {
            return true;
        }

        if (
            ! $this->hasColumn(
                $user,
                'active'
            )
        ) {
            return true;
        }

        return $this->normalizeBoolean(
            $user->getAttribute('active')
        );
    }

    /**
     * Record login metadata when the columns
     * exist on the authenticated user's table.
     */
    protected function recordLogin(
        Authenticatable $user,
        Request $request
    ): void {
        if (! $user instanceof Model) {
            return;
        }

        $data = [];

        if (
            $this->hasColumn(
                $user,
                'last_login_at'
            )
        ) {
            $data['last_login_at'] = now();
        }

        if (
            $this->hasColumn(
                $user,
                'last_login_ip'
            )
        ) {
            $data['last_login_ip'] =
                $request->ip();
        }

        if (empty($data)) {
            return;
        }

        /**
         * Query Builder is intentionally used here.
         *
         * Advantages:
         *
         * - does not require $fillable;
         * - does not trigger Model observers;
         * - does not touch updated_at;
         * - works transparently with optional columns.
         */
        $user
            ->getConnection()
            ->table(
                $user->getTable()
            )
            ->where(
                $user->getKeyName(),
                $user->getKey()
            )
            ->update($data);

        /**
         * Keep the current authenticated Model
         * synchronized with the database values.
         */
        foreach ($data as $attribute => $value) {
            $user->setAttribute(
                $attribute,
                $value
            );
        }
    }

    /**
     * Check whether the user's table contains
     * a specific column.
     */
    protected function hasColumn(
        Model $user,
        string $column
    ): bool {
        return in_array(
            $column,
            $this->getColumns($user),
            true
        );
    }

    /**
     * Retrieve and cache table columns.
     *
     * Only one schema query is required per table
     * during the request.
     */
    protected function getColumns(
        Model $user
    ): array {
        $connection =
            $user->getConnectionName()
            ?? 'default';

        $table = $user->getTable();

        $cacheKey =
            $connection . ':' . $table;

        if (
            ! array_key_exists(
                $cacheKey,
                $this->columnsCache
            )
        ) {
            $this->columnsCache[
                $cacheKey
            ] = $user
                ->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing(
                    $table
                );
        }

        return $this->columnsCache[
            $cacheKey
        ];
    }

    /**
     * Normalize boolean database values.
     *
     * Supports MySQL, PostgreSQL and
     * common scalar representations.
     */
    protected function normalizeBoolean(
        mixed $value
    ): bool {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return in_array(
                strtolower(
                    trim($value)
                ),
                [
                    '1',
                    'true',
                    't',
                    'yes',
                    'on',
                ],
                true
            );
        }

        return (bool) $value;
    }

    /**
     * Unauthorized authentication response.
     *
     * The same response is intentionally returned
     * for invalid credentials and inactive users.
     */
    protected function unauthorized(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
}

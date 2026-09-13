<?php

namespace Domain\DomainGenerator\Http\Controllers;

use Domain\DomainGenerator\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Cache das colunas das tabelas verificadas
     * durante a requisição atual.
     *
     * @var array<string, array<int, string>>
     */
    private array $columnsCache = [];

    /**
     * Realiza autenticação e retorna o token JWT.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {
        $credentials = $request->validated();

        $guard = Auth::guard('api');

        /**
         * Recupera o usuário antes do attempt para
         * verificar funcionalidades opcionais,
         * como a coluna active.
         */
        $user = $guard
            ->getProvider()
            ->retrieveByCredentials(
                $credentials
            );

        /**
         * Caso exista a coluna active e o usuário
         * esteja desativado, bloqueia a autenticação.
         *
         * Se a coluna não existir, mantém o
         * comportamento padrão.
         */
        if (
            $user !== null &&
            ! $this->canAuthenticate($user)
        ) {
            return $this->unauthorized();
        }

        /**
         * Valida as credenciais e gera o JWT.
         */
        if (
            ! $token = $guard->attempt(
                $credentials
            )
        ) {
            return $this->unauthorized();
        }

        $authenticatedUser = $guard->user();

        /**
         * Registra informações do último login
         * caso as respectivas colunas existam.
         */
        if ($authenticatedUser !== null) {
            $this->recordLogin(
                $authenticatedUser,
                $request
            );
        }

        return $this->respondWithToken(
            $token,
            $authenticatedUser
        );
    }

    /**
     * Retorna o usuário autenticado.
     */
    public function me(): JsonResponse
    {
        return $this->successResponse([
            'user' => Auth::guard('api')->user(),
        ]);
    }

    /**
     * Invalida o token atual.
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return $this->successResponse([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Renova o token JWT.
     */
    public function refresh(): JsonResponse
    {
        $guard = Auth::guard('api');

        $token = $guard->refresh();

        return $this->respondWithToken(
            $token,
            $guard->user()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | JWT Response
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o token seguindo o padrão
     * oficial de resposta da biblioteca.
     */
    protected function respondWithToken(
        string $token,
        ?Authenticatable $user = null
    ): JsonResponse {
        $guard = Auth::guard('api');

        return $this->successResponse([
            'access_token' => $token,

            'token_type' => 'Bearer',

            'expires_in' => $guard
                ->factory()
                ->getTTL() * 60,

            'user' => $user ?? $guard->user(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | User Status
    |--------------------------------------------------------------------------
    */

    /**
     * Define se o usuário pode se autenticar.
     *
     * Se a coluna active não existir, a biblioteca
     * mantém o comportamento original.
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

    /*
    |--------------------------------------------------------------------------
    | Login Metadata
    |--------------------------------------------------------------------------
    */

    /**
     * Atualiza os dados do último login.
     *
     * As colunas são opcionais.
     *
     * last_login_at
     * last_login_ip
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
         * Utilizamos Query Builder propositalmente.
         *
         * Dessa forma:
         *
         * - não depende de $fillable;
         * - não dispara Observers;
         * - não altera updated_at;
         * - funciona com colunas opcionais.
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
         * Mantém a instância atual sincronizada
         * para que os valores apareçam imediatamente
         * no retorno do login.
         */
        foreach ($data as $attribute => $value) {
            $user->setAttribute(
                $attribute,
                $value
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Schema
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica se determinada coluna existe.
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
     * Recupera as colunas da tabela.
     *
     * O resultado é armazenado em cache durante
     * a requisição para evitar múltiplas consultas
     * ao schema.
     */
    protected function getColumns(
        Model $user
    ): array {
        $connectionName =
            $user->getConnectionName()
            ?? 'default';

        $table = $user->getTable();

        $cacheKey = sprintf(
            '%s:%s',
            $connectionName,
            $table
        );

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

    /*
    |--------------------------------------------------------------------------
    | Boolean
    |--------------------------------------------------------------------------
    */

    /**
     * Normaliza diferentes representações booleanas.
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

    /*
    |--------------------------------------------------------------------------
    | Responses
    |--------------------------------------------------------------------------
    */

    /**
     * Resposta padrão de sucesso da biblioteca.
     */
    protected function successResponse(
        mixed $data = [],
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'type' => 'success',
            'status' => $status,
            'data' => $data,
        ], $status);
    }

    /**
     * Resposta padrão de erro da biblioteca.
     */
    protected function errorResponse(
        string $message,
        int $status =
            Response::HTTP_UNPROCESSABLE_ENTITY,
        array $errors = []
    ): JsonResponse {
        $response = [
            'type' => 'error',
            'status' => $status,
            'message' => $message,
            'show' => true,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json(
            $response,
            $status
        );
    }

    /**
     * Resposta utilizada quando a autenticação falha.
     */
    protected function unauthorized(): JsonResponse
    {
        return $this->errorResponse(
            'Unauthorized',
            Response::HTTP_UNAUTHORIZED
        );
    }
}

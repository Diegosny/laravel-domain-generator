<?php

namespace Domain\DomainGenerator\Abstracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class AbstractRepository
{
    /**
     * Public identifier column.
     */
    protected string $idField = 'hash';

    /**
     * Model managed by the repository.
     */
    protected Model $model;

    /**
     * Parameters that must not be interpreted as database filters.
     */
    protected array $reservedFilters = [
        'page',
        'per_page',
        'with',
        'sort',
        'order',
        'search',
    ];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    /**
     * Create a fresh query.
     */
    protected function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Public query accessor.
     *
     * Useful when a child Repository needs custom queries.
     */
    public function query(): Builder
    {
        return $this->newQuery();
    }

    /*
    |--------------------------------------------------------------------------
    | List / Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Return paginated records.
     */
    public function all(
        array $filters = [],
        array|string|null $with = [],
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        $query->where(
            $this->cleanFilters($filters)
        );

        $this->applySort(
            $query,
            $filters
        );

        $perPage = $this->resolvePerPage(
            $filters,
            $perPage
        );

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Return all records without pagination.
     */
    public function allWithoutPaginate(
        array $filters = [],
        array|string|null $with = []
    ): Collection {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        $query->where(
            $this->cleanFilters($filters)
        );

        $this->applySort(
            $query,
            $filters
        );

        return $query->get();
    }

    /**
     * Return a simple associative list.
     *
     * Example:
     *
     * [
     *     1 => 'Product A',
     *     2 => 'Product B',
     * ]
     */
    public function list(
        string $pluckValue = 'name',
        string $pluckKey = 'id',
        string $sortBy = 'name'
    ): array {
        return $this
            ->newQuery()
            ->orderBy($sortBy)
            ->pluck(
                $pluckValue,
                $pluckKey
            )
            ->all();
    }

    /**
     * Generic pluck helper.
     */
    public function pluck(
        string $column,
        ?string $key = null
    ): array {
        return $this
            ->newQuery()
            ->pluck(
                $column,
                $key
            )
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Create a record.
     */
    public function create(array $data): Model
    {
        return $this
            ->newQuery()
            ->create($data);
    }

    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    /**
     * Find by internal ID or public hash.
     *
     * Examples:
     *
     * find(10)
     * find('PAT_8F3K2Q9X')
     */
    public function find(
        int|string $id,
        array|string|null $with = []
    ): ?Model {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        return $query
            ->where(
                $this->resolveIdentifierField($id),
                $id
            )
            ->first();
    }

    /**
     * Find or fail using internal ID or public hash.
     */
    public function findOrFail(
        int|string $id,
        array|string|null $with = []
    ): Model {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        return $query
            ->where(
                $this->resolveIdentifierField($id),
                $id
            )
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update an existing Model instance.
     */
    public function update(
        Model $entity,
        array $data
    ): Model {
        $entity->fill($data);

        $entity->save();

        return $entity->refresh();
    }

    /**
     * Update or create a record.
     */
    public function updateOrCreate(
        array $attributes,
        array $values = []
    ): Model {
        return $this
            ->newQuery()
            ->updateOrCreate(
                $attributes,
                $values
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Delete by internal ID or public hash.
     */
    public function delete(
        int|string $id
    ): bool {
        $entity = $this->findOrFail($id);

        return (bool) $entity->delete();
    }

    /**
     * Delete records matching conditions.
     */
    public function deleteWhere(
        array $conditions
    ): int {
        return $this
            ->newQuery()
            ->where($conditions)
            ->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Where
    |--------------------------------------------------------------------------
    */

    /**
     * Return records matching conditions.
     */
    public function where(
        array $conditions,
        array|string|null $with = []
    ): Collection {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        return $query
            ->where($conditions)
            ->get();
    }

    /**
     * Return the first record matching conditions.
     */
    public function findOneWhere(
        array $conditions,
        array|string|null $with = []
    ): ?Model {
        $query = $this->newQuery();

        $this->applyWith(
            $query,
            $with
        );

        return $query
            ->where($conditions)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Remove technical parameters and empty filters.
     */
    protected function cleanFilters(
        array $filters
    ): array {
        return collect($filters)
            ->reject(
                fn (
                    mixed $value,
                    string $key
                ): bool => in_array(
                    $key,
                    $this->reservedFilters,
                    true
                )
            )
            ->reject(
                fn (mixed $value): bool =>
                    $value === null ||
                    $value === ''
            )
            ->all();
    }

    /**
     * Apply sorting when requested.
     */
    protected function applySort(
        Builder $query,
        array $filters
    ): void {
        $sort = $filters['sort'] ?? null;

        if (
            ! is_string($sort) ||
            trim($sort) === ''
        ) {
            return;
        }

        $order = strtolower(
            (string) (
                $filters['order'] ??
                'asc'
            )
        );

        if (
            ! in_array(
                $order,
                ['asc', 'desc'],
                true
            )
        ) {
            $order = 'asc';
        }

        $query->orderBy(
            $sort,
            $order
        );
    }

    /**
     * Resolve number of records per page.
     */
    protected function resolvePerPage(
        array $filters,
        int $default
    ): int {
        $perPage = (int) (
            $filters['per_page'] ??
            $default
        );

        if ($perPage <= 0) {
            return $default;
        }

        return min(
            $perPage,
            100
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Apply valid eager-loaded relationships.
     */
    protected function applyWith(
        Builder $query,
        array|string|null $with
    ): void {
        $relations = $this->normalizeWith(
            $with
        );

        if (empty($relations)) {
            return;
        }

        $relations = array_values(
            array_filter(
                $relations,
                fn (string $relation): bool =>
                    $this->relationExists($relation)
            )
        );

        if (! empty($relations)) {
            $query->with($relations);
        }
    }

    /**
     * Normalize relationships.
     *
     * Supports:
     *
     * ?with=checkIns
     *
     * ?with=checkIns,organization
     *
     * ?with[]=checkIns
     *
     * ?with[]=checkIns&with[]=organization
     *
     * ?with[]=checkIns,organization
     */
    protected function normalizeWith(
        array|string|null $with
    ): array {
        if (
            $with === null ||
            $with === '' ||
            $with === []
        ) {
            return [];
        }

        $values = is_array($with)
            ? $with
            : [$with];

        $relations = [];

        foreach ($values as $value) {
            if (! is_string($value)) {
                continue;
            }

            foreach (
                explode(',', $value)
                as $relation
            ) {
                $relation = trim($relation);

                if ($relation === '') {
                    continue;
                }

                $relations[] = $relation;
            }
        }

        return array_values(
            array_unique($relations)
        );
    }

    /**
     * Determine whether a relationship exists.
     *
     * Nested relationships are supported:
     *
     * checkIns.triage
     * organization.users
     */
    protected function relationExists(
        string $relation
    ): bool {
        $segments = explode(
            '.',
            $relation
        );

        $model = $this->model;

        foreach ($segments as $segment) {
            if (
                $segment === '' ||
                ! method_exists(
                    $model,
                    $segment
                )
            ) {
                return false;
            }

            try {
                $relationInstance =
                    $model->{$segment}();
            } catch (\Throwable) {
                return false;
            }

            if (
                ! $relationInstance
                    instanceof Relation
            ) {
                return false;
            }

            $model =
                $relationInstance
                    ->getRelated();
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Identifiers
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve whether the identifier should use
     * the internal primary key or public hash.
     */
    protected function resolveIdentifierField(
        int|string $id
    ): string {
        if (is_int($id)) {
            return $this->model->getKeyName();
        }

        if (
            is_string($id) &&
            ctype_digit($id)
        ) {
            return $this->model->getKeyName();
        }

        return $this->idField;
    }

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */

    /**
     * Return the repository Model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
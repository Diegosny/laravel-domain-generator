<?php

namespace Domain\DomainGenerator\Abstracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

abstract class AbstractRepository
{
    /**
     * Model managed by the repository.
     */
    protected Model $model;

    /**
     * Parameters that must never be interpreted as model filters.
     */
    protected array $reservedParameters = [
        'page',
        'per_page',
        'with',
        'sort',
        'order',
        'search',
    ];

    /**
     * Default number of records per page.
     */
    protected int $perPage = 15;

    /**
     * Maximum number of records allowed per page.
     */
    protected int $maxPerPage = 100;

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
     * Create a new query for the repository model.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /*
    |--------------------------------------------------------------------------
    | List / Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Return a paginated list of records.
     *
     * Supports:
     *
     * ?name=John
     * ?with=roles,permissions
     * ?with[]=roles&with[]=permissions
     * ?page=2
     * ?per_page=20
     * ?sort=name
     * ?order=asc
     */
    public function getAll(
        array $filters = [],
        array|string|null $with = []
    ): LengthAwarePaginator {
        $query = $this->query();

        $this->applyWith(
            $query,
            $with
        );

        $this->applyFilters(
            $query,
            $filters
        );

        $this->applySorting(
            $query,
            $filters
        );

        return $query->paginate(
            $this->resolvePerPage($filters)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find
    |--------------------------------------------------------------------------
    */

    /**
     * Find a record by internal ID or public hash.
     */
    public function find(
        mixed $identifier,
        array|string|null $with = []
    ): ?Model {
        $query = $this->query();

        $this->applyWith(
            $query,
            $with
        );

        return $this
            ->applyIdentifier(
                $query,
                $identifier
            )
            ->first();
    }

    /**
     * Find a record by internal ID or public hash.
     *
     * Throws ModelNotFoundException when not found.
     */
    public function findOrFail(
        mixed $identifier,
        array|string|null $with = []
    ): Model {
        $query = $this->query();

        $this->applyWith(
            $query,
            $with
        );

        return $this
            ->applyIdentifier(
                $query,
                $identifier
            )
            ->firstOrFail();
    }

    /**
     * Find the first record matching a field/value pair.
     */
    public function findOneWhere(
        string $field,
        mixed $value,
        array|string|null $with = []
    ): ?Model {
        $query = $this->query();

        $this->applyWith(
            $query,
            $with
        );

        return $query
            ->where($field, $value)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Where
    |--------------------------------------------------------------------------
    */

    /**
     * Start a query using a where clause.
     */
    public function where(
        string $field,
        mixed $operator = null,
        mixed $value = null
    ): Builder {
        $query = $this->query();

        /**
         * Allows:
         *
         * where('active', true)
         *
         * and:
         *
         * where('age', '>=', 18)
         */
        if (func_num_args() === 2) {
            return $query->where(
                $field,
                $operator
            );
        }

        return $query->where(
            $field,
            $operator,
            $value
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        return $this->query()->create(
            $data
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update a record using internal ID or public hash.
     */
    public function update(
        mixed $identifier,
        array $data
    ): Model {
        $model = $this->findOrFail(
            $identifier
        );

        $model->fill($data);

        $model->save();

        return $model->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Delete a record using internal ID or public hash.
     */
    public function delete(
        mixed $identifier
    ): bool {
        $model = $this->findOrFail(
            $identifier
        );

        return (bool) $model->delete();
    }

    /**
     * Delete records matching a field/value pair.
     */
    public function deleteWhere(
        string $field,
        mixed $value
    ): int {
        return $this
            ->query()
            ->where($field, $value)
            ->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Or Create
    |--------------------------------------------------------------------------
    */

    /**
     * Update an existing record or create a new one.
     */
    public function updateOrCreate(
        array $attributes,
        array $values = []
    ): Model {
        return $this
            ->query()
            ->updateOrCreate(
                $attributes,
                $values
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Pluck
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieve a list of values.
     */
    public function pluck(
        string $column,
        ?string $key = null
    ): \Illuminate\Support\Collection {
        return $this
            ->query()
            ->pluck(
                $column,
                $key
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Apply eager-loaded relationships to the query.
     *
     * Supported formats:
     *
     * with=checkIns
     *
     * with=checkIns,organization
     *
     * with[]=checkIns
     *
     * with[]=checkIns&with[]=organization
     */
    protected function applyWith(
        Builder $query,
        array|string|null $with
    ): Builder {
        $relations = $this->normalizeWith(
            $with
        );

        if (empty($relations)) {
            return $query;
        }

        $relations = array_values(
            array_filter(
                $relations,
                fn (string $relation): bool =>
                    $this->relationExists($relation)
            )
        );

        if (! empty($relations)) {
            $query->with(
                $relations
            );
        }

        return $query;
    }

    /**
     * Normalize relationship input.
     *
     * Examples:
     *
     * "checkIns"
     *
     * becomes:
     *
     * [
     *     "checkIns"
     * ]
     *
     * ---
     *
     * "checkIns,organization"
     *
     * becomes:
     *
     * [
     *     "checkIns",
     *     "organization"
     * ]
     *
     * ---
     *
     * [
     *     "checkIns",
     *     "organization"
     * ]
     *
     * remains an array.
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

        $relations = is_array($with)
            ? $with
            : [$with];

        $normalized = [];

        foreach ($relations as $relation) {
            /**
             * Ignore malformed array values such as:
             *
             * with[foo][]=bar
             */
            if (! is_string($relation)) {
                continue;
            }

            /**
             * Also supports:
             *
             * with[]=roles,permissions
             */
            foreach (
                explode(',', $relation)
                as $item
            ) {
                $item = trim($item);

                if ($item === '') {
                    continue;
                }

                $normalized[] = $item;
            }
        }

        return array_values(
            array_unique(
                $normalized
            )
        );
    }

    /**
     * Validate whether a relationship exists on the Model.
     *
     * Nested relationships are also supported:
     *
     * organization.address
     * checkIns.triage
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
                ! method_exists($model, $segment)
            ) {
                return false;
            }

            try {
                $relationship = $model->{$segment}();
            } catch (\Throwable) {
                return false;
            }

            if (! $relationship instanceof Relation) {
                return false;
            }

            $model = $relationship->getRelated();
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Apply filters to the query.
     */
    protected function applyFilters(
        Builder $query,
        array $filters
    ): Builder {
        foreach ($filters as $field => $value) {
            if (
                in_array(
                    $field,
                    $this->reservedParameters,
                    true
                )
            ) {
                continue;
            }

            if (
                $value === null ||
                $value === ''
            ) {
                continue;
            }

            /**
             * Avoid trying to filter using fields that do not
             * exist in the model table.
             */
            if (! $this->columnExists($field)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn(
                    $field,
                    $value
                );

                continue;
            }

            $query->where(
                $field,
                $value
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    /**
     * Apply ordering to the query.
     */
    protected function applySorting(
        Builder $query,
        array $filters
    ): Builder {
        $sort = $filters['sort'] ?? null;

        if (
            ! is_string($sort) ||
            $sort === '' ||
            ! $this->columnExists($sort)
        ) {
            return $query;
        }

        $order = strtolower(
            (string) ($filters['order'] ?? 'asc')
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

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve per-page value.
     */
    protected function resolvePerPage(
        array $filters
    ): int {
        $perPage = (int) (
            $filters['per_page'] ??
            $this->perPage
        );

        if ($perPage <= 0) {
            return $this->perPage;
        }

        return min(
            $perPage,
            $this->maxPerPage
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Public Identifier
    |--------------------------------------------------------------------------
    */

    /**
     * Apply internal ID or public hash to a query.
     *
     * Numeric identifiers continue using the Model primary key.
     * Public identifiers use the hash column.
     *
     * Examples:
     *
     * 15
     *
     * PAT_8F3K2Q9X
     */
    protected function applyIdentifier(
        Builder $query,
        mixed $identifier
    ): Builder {
        if (
            $this->shouldUsePublicIdentifier(
                $identifier
            )
        ) {
            return $query->where(
                'hash',
                $identifier
            );
        }

        return $query->where(
            $this->model->getKeyName(),
            $identifier
        );
    }

    /**
     * Determine whether an identifier should use the public hash.
     */
    protected function shouldUsePublicIdentifier(
        mixed $identifier
    ): bool {
        if (! $this->hasHashColumn()) {
            return false;
        }

        /**
         * Integer IDs continue using the primary key.
         */
        if (is_int($identifier)) {
            return false;
        }

        /**
         * Numeric strings such as "15" are also considered IDs.
         */
        if (
            is_string($identifier) &&
            ctype_digit($identifier)
        ) {
            return false;
        }

        return is_string($identifier);
    }

    /**
     * Determine whether the model table has a hash column.
     */
    protected function hasHashColumn(): bool
    {
        return $this->columnExists(
            'hash'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Database helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether a column exists on the model table.
     */
    protected function columnExists(
        string $column
    ): bool {
        return Schema::hasColumn(
            $this->model->getTable(),
            $column
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */

    /**
     * Return repository model.
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}

<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Campo utilizado como identificador público.
     *
     * Caso o Model implemente getRouteKeyName(),
     * esse valor será utilizado automaticamente.
     */
    protected string $routeKey = 'hash';

    /**
     * Eloquent model.
     */
    protected Model $model;

    /**
     * Create repository.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get repository model.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Create a new Eloquent query.
     */
    protected function newQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    /**
     * Resolve numeric ID or public identifier.
     */
    protected function resolveIdentifier(
        Builder $query,
        int|string $identifier
    ): Builder {

        if (is_numeric($identifier)) {
            return $query->whereKey($identifier);
        }

        $field = method_exists(
            $this->model,
            'getRouteKeyName'
        )
            ? $this->model->getRouteKeyName()
            : $this->routeKey;

        return $query->where($field, $identifier);
    }

    /**
     * Get paginated records.
     */
    public function all(
        array $filters = [],
        array|string $with = [],
        int $perPage = 10
    ): LengthAwarePaginator {

        $query = $this->newQuery()
            ->with(
                $this->normalizeWith($with)
            );

        if ($clean = $this->cleanFilters($filters)) {
            $query->where($clean);
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get all records without pagination.
     */
    public function allWithoutPaginate(
        array $filters = [],
        array|string $with = []
    ): Collection {

        $query = $this->newQuery()
            ->with(
                $this->normalizeWith($with)
            );

        if ($clean = $this->cleanFilters($filters)) {
            $query->where($clean);
        }

        return $query->get();
    }

    /**
     * Get a key/value list.
     */
    public function list(
        string $pluckValue = 'name',
        string $pluckKey = 'id',
        string $sortBy = 'name'
    ): array {

        return $this->newQuery()
            ->orderBy($sortBy)
            ->pluck(
                $pluckValue,
                $pluckKey
            )
            ->all();
    }

    /**
     * Create model.
     */
    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    /**
     * Find model by ID or public identifier.
     */
    public function find(
        int|string $id,
        array|string $with = []
    ): ?Model {

        return $this->resolveIdentifier(
            $this->newQuery()
                ->with(
                    $this->normalizeWith($with)
                ),
            $id
        )->first();
    }

    /**
     * Find model or fail.
     */
    public function findOrFail(
        int|string $id,
        array|string $with = []
    ): Model {

        $model = $this->find(
            $id,
            $with
        );

        if (! $model) {
            throw (new ModelNotFoundException)
                ->setModel(
                    get_class($this->getModel()),
                    [$id]
                );
        }

        return $model;
    }

    /**
     * Delete model.
     */
    public function delete(
        int|string $id
    ): bool {

        return (bool) $this
            ->findOrFail($id)
            ->delete();
    }

    /**
     * Update model.
     */
    public function update(Model $entity, array $data): Model
    {
        $entity->fill($data);

        $entity->save();

        return $entity->refresh();
    }

    /**
     * Find by conditions.
     */
    public function where(
        array $conditions,
        array|string $with = []
    ): Collection {

        return $this->newQuery()
            ->where($conditions)
            ->with(
                $this->normalizeWith($with)
            )
            ->get();
    }

    /**
     * Delete by conditions.
     */
    public function deleteWhere(
        array $conditions
    ): int {

        return $this->newQuery()
            ->where($conditions)
            ->delete();
    }

    /**
     * Find first by conditions.
     */
    public function findOneWhere(
        array $conditions,
        array|string $with = []
    ): ?Model {

        return $this->newQuery()
            ->where($conditions)
            ->with(
                $this->normalizeWith($with)
            )
            ->first();
    }

    /**
     * Update or create.
     */
    public function updateOrCreate(
        array $attributes,
        array $values = []
    ): Model {

        return $this->getModel()
            ->updateOrCreate(
                $attributes,
                $values
            );
    }

    /**
     * Remove technical filters.
     */
    protected function cleanFilters(
        array $filters
    ): array {

        return collect($filters)
            ->except([
                'page',
                'per_page',
                'with',
                'sort',
                'order',
                'search',
            ])
            ->filter(
                fn ($value) => $value !== null &&
                    $value !== ''
            )
            ->all();
    }

    /**
     * Normalize and validate relationships.
     */
    protected function normalizeWith(
        array|string $with
    ): array {

        $relations = is_string($with)
            ? ($with === ''
                ? []
                : explode(',', $with))
            : $with;

        return collect($relations)
            ->filter(
                fn ($relation) => method_exists(
                    $this->model,
                    $relation
                )
            )
            ->values()
            ->all();
    }
}

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
     * Default field used when the identifier is a string.
     */
    protected string $idField = 'hash';

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
     * Create a new model.
     */
    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    /**
     * Find a model by ID or configured string identifier.
     */
    public function find(
        int|string $id,
        array|string $with = []
    ): ?Model {
        $query = $this->newQuery()
            ->with(
                $this->normalizeWith($with)
            );

        if (is_numeric($id)) {
            return $query->find($id);
        }

        return $query
            ->where($this->idField, $id)
            ->first();
    }

    /**
     * Find a model or throw an exception.
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
     * Delete model by ID.
     */
    public function delete(
        int|string $id
    ): bool {
        return (bool) $this
            ->findOrFail($id)
            ->delete();
    }

    /**
     * Update an existing model.
     */
    public function update(
        Model $entity,
        array $data
    ): Model {
        $entity
            ->fill($data)
            ->save();

        return $entity;
    }

    /**
     * Get records by conditions.
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
     * Delete records by conditions.
     */
    public function deleteWhere(
        array $conditions
    ): int {
        return $this->newQuery()
            ->where($conditions)
            ->delete();
    }

    /**
     * Find first record matching conditions.
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
     * Update or create model.
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
     * Remove technical fields from filters.
     */
    protected function cleanFilters(
        array $filters
    ): array {
        $ignored = [
            'page',
            'per_page',
            'with',
            'sort',
            'order',
            'search',
        ];

        return collect($filters)
            ->except($ignored)
            ->filter(
                fn ($value) =>
                    $value !== null &&
                    $value !== ''
            )
            ->all();
    }

    /**
     * Normalize relationship list.
     */
    protected function normalizeWith(
        array|string $with
    ): array {
        if (is_string($with)) {
            return $with === ''
                ? []
                : explode(',', $with);
        }

        return $with;
    }
}
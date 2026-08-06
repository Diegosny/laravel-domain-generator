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
    protected string $idField = 'hash';
    protected Model $model;


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    protected function newQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    public function all(array $filters = [], array|string $with = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->newQuery()->with($this->normalizeWith($with));

        if ($clean = $this->cleanFilters($filters)) {
            $query->where($clean);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function allWithoutPaginate(array $filters = [], array|string $with = []): Collection
    {
        $query = $this->newQuery()->with($this->normalizeWith($with));

        if ($clean = $this->cleanFilters($filters)) {
            $query->where($clean);
        }

        return $query->get();
    }

    public function list(string $pluckValue = 'name', string $pluckKey = 'id', string $sortBy = 'name'): array
    {
        return $this->newQuery()
            ->orderBy($sortBy)
            ->pluck($pluckValue, $pluckKey)
            ->all();
    }

    public function create(array $data): Model
    {
        return $this->getModel()->create($data);
    }

    public function find(int|string $id, array|string $with = []): ?Model
    {
        if (is_numeric($id)) {
            return $this->newQuery()->with($this->normalizeWith($with))->find($id);
        }

        return $this->findOneWhere([$this->idField => $id], $with);
    }

    public function findOrFail(int|string $id, array|string $with = []): Model
    {
        $model = $this->find($id, $with);

        if (! $model) {
            throw (new ModelNotFoundException)->setModel(get_class($this->getModel()), [$id]);
        }

        return $model;
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    public function update(Model $entity, array $data): Model
    {
        $entity->fill($data)->save();

        return $entity;
    }

    public function where(array $conditions, array|string $with = []): Collection
    {
        return $this->newQuery()->where($conditions)->with($this->normalizeWith($with))->get();
    }

    public function deleteWhere(array $conditions): int
    {
        return $this->newQuery()->where($conditions)->delete();
    }

    public function findOneWhere(array $conditions, array|string $with = []): ?Model
    {
        return $this->newQuery()->where($conditions)->with($this->normalizeWith($with))->first();
    }

    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->getModel()->updateOrCreate($attributes, $values);
    }

    protected function cleanFilters(array $filters): array
    {
        $ignored = ['page', 'per_page', 'with', 'sort', 'order', 'search'];

        return collect($filters)
            ->except($ignored)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();
    }

    protected function normalizeWith(array|string $with): array
    {
        if (is_string($with)) {
            return $with === '' ? [] : explode(',', $with);
        }

        return $with;
    }
}
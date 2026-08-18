<?php

namespace Domain\DomainGenerator\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contrato base para repositórios Eloquent.
 *
 * @template TModel of Model
 */
interface RepositoryInterface
{
    /**
     * @return TModel
     */
    public function getModel(): Model;

    /**
     * Lista paginada, com filtros simples (where) e eager loading opcionais.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>|string  $with
     */
    public function all(array $filters = [], array|string $with = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Lista completa (sem paginação), com filtros e eager loading opcionais.
     *
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>|string  $with
     * @return Collection<int, TModel>
     */
    public function allWithoutPaginate(array $filters = [], array|string $with = []): Collection;

    /**
     * Retorna um array chave => valor pronto para uso em <select>.
     *
     * @return array<int|string, mixed>
     */
    public function list(string $pluckValue = 'name', string $pluckKey = 'id', string $sortBy = 'name'): array;

    /**
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * Busca por id numérico ou pelo campo identificador público (ex: hash).
     *
     * @param  array<int, string>|string  $with
     * @return TModel|null
     */
    public function find(int|string $id, array|string $with = []): ?Model;

    /**
     * Igual a find(), mas lança ModelNotFoundException se não encontrar.
     *
     * @param  array<int, string>|string  $with
     * @return TModel
     */
    public function findOrFail(int|string $id, array|string $with = []): Model;

    public function delete(int|string $id): bool;

    /**
     * @param  TModel  $entity
     * @param  array<string, mixed>  $data
     * @return TModel
     */
    public function update(Model $entity, array $data): Model;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  array<int, string>|string  $with
     * @return Collection<int, TModel>
     */
    public function where(array $conditions, array|string $with = []): Collection;

    /**
     * @param  array<string, mixed>  $conditions
     * @return int Número de registros removidos.
     */
    public function deleteWhere(array $conditions): int;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  array<int, string>|string  $with
     * @return TModel|null
     */
    public function findOneWhere(array $conditions, array|string $with = []): ?Model;

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;
}

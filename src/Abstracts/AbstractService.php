<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

abstract class AbstractService implements ServiceInterface
{
    protected array $with = [];

    protected mixed $repository;

    public function getAll(array $params = [], array|string $with = []): mixed
    {
        return $this->repository->all($params, $with);
    }

    public function find(mixed $id, array $with = []): mixed
    {
        $result = $this->repository->find($id, $with);

        if ($result === null) {
            throw new ModelNotFoundException('Objeto não encontrado na base de dados');
        }

        return $result;
    }

    public function beforeSave(array $data): array
    {
        return $data;
    }

    public function save(array $data): mixed
    {
        $data = $this->beforeSave($data);

        if (! $this->validateOnInsert($data)) {
            return [];
        }

        $entity = $this->repository->create($data);

        $this->afterSave($entity, $data);

        return $entity;
    }

    public function afterSave(mixed $entity, array $params): mixed
    {
        return $entity;
    }

    public function beforeUpdate(mixed $id, array $data): array
    {
        return $data;
    }

    public function update(mixed $id, array $data): mixed
    {
        $data = $this->beforeUpdate($id, $data);

        if (! $this->validateOnUpdate($id, $data)) {
            return false;
        }

        $entity = $this->find($id);
        $updated = $this->repository->update($entity, $data);

        if ($updated) {
            $this->afterUpdate($entity, $data);
        }

        return $updated;
    }

    public function afterUpdate(mixed $entity, array $params): mixed
    {
        return $entity;
    }

    public function beforeDelete(mixed $id): mixed
    {
        return $id;
    }

    public function delete(mixed $id): mixed
    {
        $this->validateOnDelete($id);
        $this->beforeDelete($id);

        $this->repository->delete($id);

        $this->afterDelete($id);

        return $id;
    }

    public function afterDelete(mixed $id): mixed
    {
        return $id;
    }

    public function toSelect(bool $withGenerateSelectOption = true): mixed
    {
        $items = $this->repository->list();

        if ($withGenerateSelectOption) {
            return generateSelectOption($items);
        }

        return $items;
    }

    public function validateOnInsert(array $params): bool
    {
        return true;
    }

    public function validateOnUpdate(mixed $id, array $params): bool
    {
        return true;
    }

    public function validateOnDelete(mixed $id): bool
    {
        $this->find($id);

        return true;
    }

    public function getRepository(): object
    {
        return $this->repository;
    }

    public function getUserAuth(): mixed
    {
        return Auth::user();
    }

    public function preRequisite(mixed $id = null): array
    {
        return [];
    }

    public function create(array $data): mixed
    {
        $entity = $this->repository->create($data);

        $this->afterSave($entity, $data);

        return $entity;
    }

    public function findOneWhere(array $where, array $with = []): ?object
    {
        return $this->repository->findOneWhere($where, $with);
    }

    public function updateOrCreate(array $paramsValidation, array $params): mixed
    {
        return $this->repository->updateOrCreate($paramsValidation, $params);
    }
}
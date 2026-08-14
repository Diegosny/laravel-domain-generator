<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\DTOInterface;
use Domain\DomainGenerator\Interfaces\ServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

abstract class AbstractService implements ServiceInterface
{
    /**
     * Default relationships.
     */
    protected array $with = [];

    /**
     * Repository used by the service.
     */
    protected mixed $repository;

    /**
     * Get all records.
     */
    public function getAll(
        array $params = [],
        array|string $with = []
    ): mixed {
        return $this->repository->all(
            $params,
            $with
        );
    }

    /**
     * Find a record.
     */
    public function find(
        mixed $id,
        array|string $with = []
    ): mixed {
        $result = $this->repository->find(
            $id,
            $with
        );

        if ($result === null) {
            throw new ModelNotFoundException(
                'Objeto não encontrado na base de dados'
            );
        }

        return $result;
    }

    // ---------------------------------------------------------
    // Store
    // ---------------------------------------------------------

    /**
     * Hook executed before saving.
     *
     * Kept as array for backwards compatibility.
     */
    public function beforeSave(
        array $data
    ): array {
        return $data;
    }

    /**
     * Save entity using array data.
     *
     * This method is intentionally kept compatible
     * with previous versions of the library.
     */
    public function save(
        array $data
    ): mixed {
        $data = $this->beforeSave($data);

        if (! $this->validateOnInsert($data)) {
            return [];
        }

        $entity = $this->repository->create($data);

        $this->afterSave(
            $entity,
            $data
        );

        return $entity;
    }

    /**
     * Save entity using a DTO.
     *
     * DTO is converted to an array before reaching
     * the original save() flow.
     */
    public function saveDto(
        DTOInterface $dto
    ): mixed {
        return $this->save(
            $dto->toArray()
        );
    }

    /**
     * Hook executed after saving.
     */
    public function afterSave(
        mixed $entity,
        array $params
    ): mixed {
        return $entity;
    }

    // ---------------------------------------------------------
    // Update
    // ---------------------------------------------------------

    /**
     * Hook executed before updating.
     */
    public function beforeUpdate(
        mixed $id,
        array $data
    ): array {
        return $data;
    }

    /**
     * Update entity using array data.
     */
    public function update(
        mixed $id,
        array $data
    ): mixed {
        $data = $this->beforeUpdate(
            $id,
            $data
        );

        if (! $this->validateOnUpdate(
            $id,
            $data
        )) {
            return false;
        }

        $entity = $this->find($id);

        $updated = $this->repository->update(
            $entity,
            $data
        );

        if ($updated) {
            $this->afterUpdate(
                $entity,
                $data
            );
        }

        return $updated;
    }

    /**
     * Update entity using a DTO.
     *
     * DTO is converted to an array before reaching
     * the original update() flow.
     */
    public function updateDto(
        mixed $id,
        DTOInterface $dto
    ): mixed {
        return $this->update(
            $id,
            $dto->toArray()
        );
    }

    /**
     * Hook executed after updating.
     */
    public function afterUpdate(
        mixed $entity,
        array $params
    ): mixed {
        return $entity;
    }

    // ---------------------------------------------------------
    // Delete
    // ---------------------------------------------------------

    /**
     * Hook executed before deleting.
     */
    public function beforeDelete(
        mixed $id
    ): mixed {
        return $id;
    }

    /**
     * Delete entity.
     */
    public function delete(
        mixed $id
    ): mixed {
        $this->validateOnDelete($id);

        $this->beforeDelete($id);

        $this->repository->delete($id);

        $this->afterDelete($id);

        return $id;
    }

    /**
     * Hook executed after deleting.
     */
    public function afterDelete(
        mixed $id
    ): mixed {
        return $id;
    }

    // ---------------------------------------------------------
    // Validation
    // ---------------------------------------------------------

    /**
     * Validate before insert.
     */
    public function validateOnInsert(
        array $params
    ): bool {
        return true;
    }

    /**
     * Validate before update.
     */
    public function validateOnUpdate(
        mixed $id,
        array $params
    ): bool {
        return true;
    }

    /**
     * Validate before delete.
     */
    public function validateOnDelete(
        mixed $id
    ): bool {
        $this->find($id);

        return true;
    }

    // ---------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------

    /**
     * Get repository.
     */
    public function getRepository(): object
    {
        return $this->repository;
    }

    /**
     * Get authenticated user.
     */
    public function getUserAuth(): mixed
    {
        return Auth::user();
    }

    /**
     * Return prerequisites.
     */
    public function preRequisite(
        mixed $id = null
    ): array {
        return [];
    }

    /**
     * Create entity directly.
     */
    public function create(
        array $data
    ): mixed {
        $entity = $this->repository->create(
            $data
        );

        $this->afterSave(
            $entity,
            $data
        );

        return $entity;
    }

    /**
     * Create entity using DTO.
     */
    public function createDto(
        DTOInterface $dto
    ): mixed {
        return $this->create(
            $dto->toArray()
        );
    }

    /**
     * Find one entity by conditions.
     */
    public function findOneWhere(
        array $where,
        array|string $with = []
    ): ?object {
        return $this->repository->findOneWhere(
            $where,
            $with
        );
    }

    /**
     * Update or create entity.
     */
    public function updateOrCreate(
        array $paramsValidation,
        array $params
    ): mixed {
        return $this->repository->updateOrCreate(
            $paramsValidation,
            $params
        );
    }

    /**
     * Update or create entity using DTOs.
     */
    public function updateOrCreateDto(
        DTOInterface $paramsValidation,
        DTOInterface $params
    ): mixed {
        return $this->updateOrCreate(
            $paramsValidation->toArray(),
            $params->toArray()
        );
    }

    /**
     * Return select options.
     */
    public function toSelect(
        bool $withGenerateSelectOption = true
    ): mixed {
        $items = $this->repository->list();

        if ($withGenerateSelectOption) {
            return generateSelectOption($items);
        }

        return $items;
    }
}
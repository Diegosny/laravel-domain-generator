<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\DTOInterface;
use Illuminate\Support\Facades\Auth;

abstract class AbstractService
{
    /**
     * Default relationships.
     */
    protected array $with = [];

    /**
     * Repository used by the Service.
     */
    protected mixed $repository;

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    /**
     * Return paginated records.
     */
    public function getAll(
        array $params = [],
        array|string|null $with = []
    ): mixed {
        return $this->repository->all(
            $params,
            $this->resolveWith($with)
        );
    }

    /**
     * Find by internal ID or public hash.
     */
    public function find(
        mixed $id,
        array|string|null $with = []
    ): mixed {
        return $this->repository->find(
            $id,
            $this->resolveWith($with)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    /**
     * Hook executed before save.
     */
    protected function beforeSave(
        array $data
    ): array {
        return $data;
    }

    /**
     * Save data using the complete lifecycle.
     */
    public function save(
        array $data
    ): mixed {
        $data = $this->beforeSave(
            $data
        );

        if (! $this->validateOnInsert($data)) {
            return [];
        }

        $entity = $this->repository->create(
            $data
        );

        return $this->afterSave(
            $entity,
            $data
        );
    }

    /**
     * Save a DTO.
     */
    public function saveDto(
        DTOInterface $dto
    ): mixed {
        return $this->save(
            $dto->toArray()
        );
    }

    /**
     * Hook executed after save.
     */
    protected function afterSave(
        mixed $entity,
        array $data
    ): mixed {
        return $entity;
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Hook executed before update.
     */
    protected function beforeUpdate(
        mixed $id,
        array $data
    ): array {
        return $data;
    }

    /**
     * Update entity.
     */
    public function update(
        mixed $id,
        array $data
    ): mixed {
        $data = $this->beforeUpdate(
            $id,
            $data
        );

        if (
            ! $this->validateOnUpdate(
                $id,
                $data
            )
        ) {
            return false;
        }

        $entity = $this->repository->find(
            $id
        );

        if ($entity === null) {
            return false;
        }

        $entity = $this->repository->update(
            $entity,
            $data
        );

        return $this->afterUpdate(
            $entity,
            $data
        );
    }

    /**
     * Update using DTO.
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
     * Hook executed after update.
     */
    protected function afterUpdate(
        mixed $entity,
        array $data
    ): mixed {
        return $entity;
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Hook executed before delete.
     */
    protected function beforeDelete(
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
        if (! $this->validateOnDelete($id)) {
            return false;
        }

        $id = $this->beforeDelete(
            $id
        );

        $this->repository->delete(
            $id
        );

        return $this->afterDelete(
            $id
        );
    }

    /**
     * Hook executed after delete.
     */
    protected function afterDelete(
        mixed $id
    ): mixed {
        return $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation hooks
    |--------------------------------------------------------------------------
    */

    /**
     * Validate insert operation.
     */
    protected function validateOnInsert(
        array $data
    ): bool {
        return true;
    }

    /**
     * Validate update operation.
     */
    protected function validateOnUpdate(
        mixed $id,
        array $data
    ): bool {
        return true;
    }

    /**
     * Validate delete operation.
     */
    protected function validateOnDelete(
        mixed $id
    ): bool {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Direct Create
    |--------------------------------------------------------------------------
    */

    /**
     * Create directly through the Repository.
     *
     * Unlike save(), this method does not execute
     * beforeSave() or validateOnInsert().
     */
    public function create(
        array $data
    ): mixed {
        $entity = $this->repository->create(
            $data
        );

        return $this->afterSave(
            $entity,
            $data
        );
    }

    /**
     * Create directly using DTO.
     */
    public function createDto(
        DTOInterface $dto
    ): mixed {
        return $this->create(
            $dto->toArray()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Find Where
    |--------------------------------------------------------------------------
    */

    /**
     * Return first record matching conditions.
     */
    public function findOneWhere(
        array $where,
        array|string|null $with = []
    ): ?object {
        return $this->repository->findOneWhere(
            $where,
            $this->resolveWith($with)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Or Create
    |--------------------------------------------------------------------------
    */

    /**
     * Update or create.
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
     * Update or create using DTOs.
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

    /*
    |--------------------------------------------------------------------------
    | Prerequisites
    |--------------------------------------------------------------------------
    */

    /**
     * Additional data required by forms/screens.
     */
    public function preRequisite(
        mixed $id = null
    ): array {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Select
    |--------------------------------------------------------------------------
    */

    /**
     * Return records formatted for select components.
     */
    public function toSelect(
        bool $withGenerateSelectOption = true
    ): mixed {
        $items = $this->repository->list();

        if (! $withGenerateSelectOption) {
            return $items;
        }

        return $this->generateSelectOption(
            $items
        );
    }

    /**
     * Convert associative list into value/label format.
     *
     * Input:
     *
     * [
     *     1 => 'Product A',
     *     2 => 'Product B',
     * ]
     *
     * Output:
     *
     * [
     *     [
     *         'value' => 1,
     *         'label' => 'Product A',
     *     ],
     * ]
     */
    protected function generateSelectOption(
        array $items
    ): array {
        $options = [];

        foreach ($items as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Use relationships received by the caller or,
     * when absent, use Service defaults.
     */
    protected function resolveWith(
        array|string|null $with
    ): array|string {
        if (
            $with === null ||
            $with === '' ||
            $with === []
        ) {
            return $this->with;
        }

        return $with;
    }

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    */

    /**
     * Return repository.
     */
    public function getRepository(): object
    {
        return $this->repository;
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    /**
     * Return authenticated user.
     */
    public function getUserAuth(): mixed
    {
        return Auth::user();
    }
}
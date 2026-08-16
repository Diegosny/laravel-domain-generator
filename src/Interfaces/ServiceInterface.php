<?php

namespace Domain\DomainGenerator\Interfaces;

interface ServiceInterface
{
    public function validateOnInsert(array $params): bool;

    public function validateOnUpdate(mixed $id, array $params): bool;

    public function validateOnDelete(mixed $id): bool;

    public function afterSave(mixed $entity, array $params): mixed;

    public function afterUpdate(mixed $entity, array $params): mixed;
}

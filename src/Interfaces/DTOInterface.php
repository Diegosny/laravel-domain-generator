<?php

namespace Domain\DomainGenerator\Interfaces;

interface DTOInterface
{
    public static function fromArray(
        array $data
    ): static;

    public function toArray(): array;
}
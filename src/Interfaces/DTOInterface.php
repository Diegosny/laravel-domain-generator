<?php

namespace Domain\DomainGenerator\Interfaces;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

interface DTOInterface extends Arrayable, JsonSerializable
{
    /**
     * Create a DTO from an array.
     */
    public static function fromArray(array $data): static;

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array;

    /**
     * Convert the DTO to a JSON serializable value.
     */
    public function jsonSerialize(): array;
}

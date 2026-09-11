<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\DTOInterface;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionClass;
use ReflectionParameter;

abstract class AbstractDTO implements DTOInterface, Arrayable, JsonSerializable
{
    /**
     * Create DTO from an associative array.
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $arguments[] = static::resolveParameter(
                $parameter,
                $data
            );
        }

        return $reflection->newInstanceArgs(
            $arguments
        );
    }

    /**
     * Resolve a constructor parameter from input data.
     */
    protected static function resolveParameter(
        ReflectionParameter $parameter,
        array $data
    ): mixed {
        $name = $parameter->getName();

        if (array_key_exists($name, $data)) {
            return $data[$name];
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Missing required property [%s] for DTO [%s].',
                $name,
                static::class
            )
        );
    }

    /**
     * Convert DTO to array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
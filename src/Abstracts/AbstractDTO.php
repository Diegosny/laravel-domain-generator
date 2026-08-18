<?php

namespace Domain\DomainGenerator\Abstracts;

use Domain\DomainGenerator\Interfaces\DTOInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionParameter;

abstract class AbstractDTO implements DTOInterface
{
    use ReflectionNamedType;

    /**
     * Create a DTO from an array.
     *
     * The array keys must match the constructor parameter names.
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new static;
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $arguments[] = static::resolveParameter(
                $parameter,
                $data
            );
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Resolve a constructor parameter from the given data.
     */
    protected static function resolveParameter(
        ReflectionParameter $parameter,
        array $data
    ): mixed {
        $name = $parameter->getName();

        /*
         * The field was provided.
         */
        if (array_key_exists($name, $data)) {
            $value = $data[$name];
            $type = $parameter->getType();

            if (
                $type instanceof ReflectionNamedType &&
                enum_exists($type->getName()) &&
                is_subclass_of($type->getName(), \BackedEnum::class)
            ) {
                return $type->getName()::from($value);
            }

            return $value;
        }

        /*
         * The parameter has a default value.
         */
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        /*
         * The parameter allows null.
         */
        if ($parameter->allowsNull()) {
            return null;
        }

        /*
         * The field is required but was not provided.
         */
        throw new InvalidArgumentException(
            sprintf(
                'Required field [%s] was not provided for DTO [%s].',
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
     * Convert DTO to JSON serializable array.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

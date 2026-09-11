<?php

namespace Domain\DomainGenerator\Abstracts;

use BackedEnum;
use Domain\DomainGenerator\Interfaces\DTOInterface;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use UnitEnum;
use ValueError;

abstract class AbstractDTO implements DTOInterface, Arrayable, JsonSerializable
{
    /**
     * Create a DTO from an associative array.
     *
     * Constructor types are inspected automatically so values
     * such as backed Enums can be hydrated from their scalar
     * representation.
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(
            static::class
        );

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];

        foreach (
            $constructor->getParameters()
            as $parameter
        ) {
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
     * Resolve a constructor parameter.
     */
    protected static function resolveParameter(
        ReflectionParameter $parameter,
        array $data
    ): mixed {
        $name = $parameter->getName();

        if (! array_key_exists($name, $data)) {
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

        $value = $data[$name];

        /**
         * Explicit null is valid only for nullable parameters.
         */
        if ($value === null) {
            if ($parameter->allowsNull()) {
                return null;
            }

            return $value;
        }

        return static::castValue(
            $value,
            $parameter->getType(),
            $name
        );
    }

    /**
     * Convert an input value according to the constructor type.
     */
    protected static function castValue(
        mixed $value,
        ?ReflectionType $type,
        string $parameter
    ): mixed {
        if ($type === null) {
            return $value;
        }

        /**
         * Union types.
         *
         * Example:
         *
         * EnumArrivalType|string
         */
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                try {
                    return static::castValue(
                        $value,
                        $unionType,
                        $parameter
                    );
                } catch (
                    InvalidArgumentException |
                    ValueError
                ) {
                    // Try the next union type.
                }
            }

            return $value;
        }

        if (! $type instanceof ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();

        /**
         * Native PHP scalar types do not need DTO hydration.
         */
        if ($type->isBuiltin()) {
            return static::castBuiltinValue(
                $value,
                $typeName
            );
        }

        /**
         * Already hydrated.
         *
         * Example:
         *
         * EnumArrivalType::WALK_IN
         */
        if ($value instanceof $typeName) {
            return $value;
        }

        /**
         * PHP Enum.
         */
        if (enum_exists($typeName)) {
            return static::castEnumValue(
                $typeName,
                $value,
                $parameter
            );
        }

        /**
         * Nested DTO.
         *
         * Example:
         *
         * public AddressDTO $address
         */
        if (
            is_array($value) &&
            is_a(
                $typeName,
                DTOInterface::class,
                true
            )
        ) {
            return $typeName::fromArray(
                $value
            );
        }

        return $value;
    }

    /**
     * Cast supported built-in PHP types.
     */
    protected static function castBuiltinValue(
        mixed $value,
        string $type
    ): mixed {
        return match ($type) {
            'int' => is_numeric($value)
                ? (int) $value
                : $value,

            'float' => is_numeric($value)
                ? (float) $value
                : $value,

            'string' => is_scalar($value)
                ? (string) $value
                : $value,

            'bool' => static::castBoolean(
                $value
            ),

            'array' => is_array($value)
                ? $value
                : $value,

            default => $value,
        };
    }

    /**
     * Normalize boolean representations.
     */
    protected static function castBoolean(
        mixed $value
    ): mixed {
        if (is_bool($value)) {
            return $value;
        }

        if (
            $value === 1 ||
            $value === '1' ||
            $value === 'true'
        ) {
            return true;
        }

        if (
            $value === 0 ||
            $value === '0' ||
            $value === 'false'
        ) {
            return false;
        }

        return $value;
    }

    /**
     * Hydrate PHP Enums.
     *
     * Backed Enum:
     *
     * "walk_in"
     *
     * becomes:
     *
     * EnumArrivalType::WALK_IN
     *
     * Unit Enum:
     *
     * "ACTIVE"
     *
     * becomes:
     *
     * Status::ACTIVE
     */
    protected static function castEnumValue(
        string $enumClass,
        mixed $value,
        string $parameter
    ): UnitEnum {
        /**
         * Backed enums.
         */
        if (
            is_subclass_of(
                $enumClass,
                BackedEnum::class
            )
        ) {
            try {
                return $enumClass::from(
                    $value
                );
            } catch (ValueError $exception) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid value [%s] for Enum [%s] on DTO property [%s].',
                        is_scalar($value)
                            ? (string) $value
                            : get_debug_type($value),
                        $enumClass,
                        $parameter
                    ),
                    previous: $exception
                );
            }
        }

        /**
         * Unit enums do not have ->value,
         * so they are resolved by case name.
         */
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $value) {
                return $case;
            }
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid case [%s] for Enum [%s] on DTO property [%s].',
                is_scalar($value)
                    ? (string) $value
                    : get_debug_type($value),
                $enumClass,
                $parameter
            )
        );
    }

    /**
     * Convert DTO to array.
     *
     * Enums are converted back to values suitable for
     * persistence and transport.
     */
    public function toArray(): array
    {
        $data = [];

        foreach (
            get_object_vars($this)
            as $key => $value
        ) {
            $data[$key] = static::normalizeValue(
                $value
            );
        }

        return $data;
    }

    /**
     * Normalize DTO values before persistence.
     */
    protected static function normalizeValue(
        mixed $value
    ): mixed {
        /**
         * Backed Enum:
         *
         * EnumArrivalType::WALK_IN
         *
         * becomes:
         *
         * "walk_in"
         */
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        /**
         * Unit Enum:
         *
         * Status::ACTIVE
         *
         * becomes:
         *
         * "ACTIVE"
         */
        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        /**
         * Nested DTO.
         */
        if ($value instanceof DTOInterface) {
            return $value->toArray();
        }

        /**
         * Laravel Arrayable objects.
         */
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        /**
         * Recursive arrays.
         */
        if (is_array($value)) {
            return array_map(
                fn (mixed $item) =>
                    static::normalizeValue($item),
                $value
            );
        }

        return $value;
    }

    /**
     * JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
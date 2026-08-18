<?php

namespace Domain\DomainGenerator\Traits;

use Illuminate\Support\Str;

trait HasHash
{
    /**
     * Generate UUID automatically.
     */
    protected static function bootHasHash(): void
    {
        static::creating(function ($model) {

            if ($model->hash) {
                return;
            }

            $strategy = config(
                'domain-generator.identifier.strategy',
                'ulid'
            );

            $model->hash = match ($strategy) {

                'uuid' => (string) Str::uuid(),

                'uuid32' => str_replace(
                    '-',
                    '',
                    (string) Str::uuid()
                ),

                'ulid' => (string) Str::ulid(),

                default => (string) Str::ulid(),
            };
        });
    }

    /**
     * Use hash for Route Model Binding.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    /**
     * Return public identifier.
     */
    public function getPublicIdentifier(): string
    {
        return $this->hash;
    }
}

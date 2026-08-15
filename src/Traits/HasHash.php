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

            if (empty($model->hash)) {
                $model->hash = (string) Str::uuid();
            }
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
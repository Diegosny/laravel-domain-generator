<?php

namespace Domain\DomainGenerator\Traits;

use Domain\DomainGenerator\Support\PublicIdGenerator;

trait HasHash
{
    protected static function bootHasHash(): void
    {
        static::creating(function ($model) {

            if (filled($model->hash)) {
                return;
            }

            do {
                $hash = PublicIdGenerator::generate(
                    $model->getHashPrefix(),
                    config('domain-generator.public_id.length', 8)
                );
            } while (
                $model->newQuery()
                    ->where('hash', $hash)
                    ->exists()
            );

            $model->hash = $hash;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    public function getHashPrefix(): string
    {
        return property_exists($this, 'hashPrefix')
            ? $this->hashPrefix
            : strtoupper(substr(class_basename($this), 0, 3));
    }
}
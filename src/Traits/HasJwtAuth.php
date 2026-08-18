<?php

namespace DiegoSny\LaravelDomainGenerator\Traits;

use DiegoSny\LaravelDomainGenerator\Support\PublicIdGenerator;

trait HasHash
{
    protected static function bootHasHash(): void
    {
        static::creating(function ($model) {

            if (! empty($model->hash)) {
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

    /**
     * Utiliza o hash nas rotas automaticamente.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    /**
     * Retorna o prefixo do hash.
     *
     * Se a Model possuir a propriedade $hashPrefix,
     * ela será utilizada. Caso contrário, utiliza
     * as três primeiras letras da classe.
     */
    public function getHashPrefix(): string
    {
        if (property_exists($this, 'hashPrefix')) {
            return $this->hashPrefix;
        }

        return strtoupper(substr(class_basename($this), 0, 3));
    }
}

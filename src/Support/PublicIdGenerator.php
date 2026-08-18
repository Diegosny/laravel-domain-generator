<?php

namespace DiegoSny\LaravelDomainGenerator\Support;

class PublicIdGenerator
{
    /**
     * Alfabeto sem caracteres ambíguos.
     */
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function generate(string $prefix, int $length = 8): string
    {
        return strtoupper($prefix).'_'.self::randomCode($length);
    }

    private static function randomCode(int $length): string
    {
        $alphabet = self::ALPHABET;
        $max = strlen($alphabet) - 1;

        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
    }
}
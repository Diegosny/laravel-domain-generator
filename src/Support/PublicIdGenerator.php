<?php

namespace Domain\DomainGenerator\Support;

class PublicIdGenerator
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function generate(string $prefix, int $length = 8): string
    {
        return strtoupper($prefix).'_'.self::random($length);
    }

    private static function random(int $length): string
    {
        $result = '';

        $max = strlen(self::ALPHABET) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= self::ALPHABET[random_int(0, $max)];
        }

        return $result;
    }
}
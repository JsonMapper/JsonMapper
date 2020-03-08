<?php declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Helpers;


class TypeHelper
{
    public static function isScalarType(string $type): bool
    {
        return $type === 'string'
            || $type === 'boolean' || $type === 'bool'
            || $type === 'integer' || $type === 'int'
            || $type === 'double' || $type === 'float';
    }

    public static function isBuiltinClass(string $type): bool
    {
        return $type === '\\' . \DateTimeImmutable::class
            || $type === '\\' . \DateTime::class;
    }
}
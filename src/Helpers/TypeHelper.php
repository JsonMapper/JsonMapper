<?php

declare(strict_types=1);

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

    /**
     * @param string|bool|int|float $value
     * @return string|bool|int|float
     */
    public static function cast($value, string $type)
    {
        if ($type === 'string') {
            return (string) $value;
        }
        if ($type === 'bool') {
            return (bool) $value;
        }
        if ($type === 'int') {
            return (int) $value;
        }
        if ($type === 'float') {
            return (float) $value;
        }

        return $value;
    }
}

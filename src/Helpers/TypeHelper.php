<?php

declare(strict_types=1);

namespace JsonMapper\Helpers;

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
        if (strpos($type, '\\') === 0) {
            $type = substr($type, 1);
        }
        return $type === \DateTimeImmutable::class
            || $type === \DateTime::class;
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

    public static function isCustomClass(string $type): bool
    {
        return ! self::isScalarType($type) && ! self::isBuiltinClass($type) && ! self::isArray($type);
    }

    public static function isArray(string $type, ?string &$innertype = null): bool
    {
        if (strlen($type) <= 2 || substr_compare($type, '[]', -2, 2) !== 0) {
            return false;
        }

        $innertype = substr($type, 0, -2);

        return true;
    }
}

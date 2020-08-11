<?php

declare(strict_types=1);

namespace JsonMapper\Helpers;

use JsonMapper\Enums\ScalarType;

class ClassHelper
{
    private const BUILTIN_CLASSES = [
        \DateTimeImmutable::class,
        \DateTime::class,
    ];

    public static function isBuiltin(string $type): bool
    {
        if (ScalarType::isValid($type) || $type === 'mixed') {
            return false;
        }

        if (!class_exists($type)) {
            return false;
        }

        if (strpos($type, '\\') === 0) {
            $type = substr($type, 1);
        }

        return in_array($type, self::BUILTIN_CLASSES, true);
    }

    public static function isCustom(string $type): bool
    {
        if (ScalarType::isValid($type) || $type === 'mixed') {
            return false;
        }

        if (!class_exists($type)) {
            return false;
        }

        return !self::isBuiltin($type);
    }
}

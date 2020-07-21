<?php

declare(strict_types=1);

namespace JsonMapper\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static ScalarType STRING()
 * @method static ScalarType BOOLEAN()
 * @method static ScalarType BOOL()
 * @method static ScalarType INTEGER()
 * @method static ScalarType INT()
 * @method static ScalarType DOUBLE()
 * @method static ScalarType FLOAT()
 */
class ScalarType extends Enum
{
    private const STRING = 'string';
    private const BOOLEAN = 'boolean';
    private const BOOL = 'bool';
    private const INTEGER = 'integer';
    private const INT = 'int';
    private const DOUBLE = 'double';
    private const FLOAT = 'float';

    /**
     * @param string|bool|int|float $value
     * @return string|bool|int|float
     */
    public function cast($value)
    {
        if ($this->value === self::STRING) {
            return (string) $value;
        }
        if ($this->value === self::BOOLEAN || $this->value === self::BOOL) {
            return (bool) $value;
        }
        if ($this->value === self::INTEGER || $this->value === self::INT) {
            return (int) $value;
        }
        if ($this->value === self::DOUBLE || $this->value === self::FLOAT) {
            return (float) $value;
        }

        throw new \LogicException("Missing {$this->value} in cast method");
    }
}

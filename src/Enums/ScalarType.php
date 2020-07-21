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
 * @method static ScalarType MIXED()
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
    private const MIXED = 'mixed';

    /**
     * @param string|bool|int|float $value
     * @return string|bool|int|float
     */
    public function cast($value)
    {
        if ($this->equals(self::MIXED())) {
            return $value;
        }
        if ($this->equals(self::STRING())) {
            return (string) $value;
        }
        if ($this->equals(self::BOOLEAN()) || $this->equals(self::BOOL())) {
            return (bool) $value;
        }
        if ($this->equals(self::INTEGER()) || $this->equals(self::INT())) {
            return (int) $value;
        }
        if ($this->equals(self::DOUBLE()) || $this->equals(self::FLOAT())) {
            return (float) $value;
        }

        throw new \LogicException("Missing {$this->value} in cast method");
    }
}

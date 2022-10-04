<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware\Constructor;

use JsonMapper\Middleware\Constructor\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\Constructor\Parameter
     */
    public function testCanHoldProperties(): void
    {
        $parameter = new Parameter(
            $name = 'id',
            $type = 'int',
            $position = 1,
            $defaultValue = null
        );

        self::assertSame($name, $parameter->getName());
        self::assertSame($type, $parameter->getType());
        self::assertSame($position, $parameter->getPosition());
        self::assertSame($defaultValue, $parameter->getDefaultValue());
    }
}

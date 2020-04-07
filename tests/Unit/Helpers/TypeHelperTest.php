<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Helpers;

use JsonMapper\Helpers\TypeHelper;
use PHPUnit\Framework\TestCase;

class TypeHelperTest extends TestCase
{
    /**
     * @covers \JsonMapper\Helpers\TypeHelper
     * @dataProvider scalarTypesDataProvider
     */
    public function testScalarTypesAreSeenAsScalar(string $type): void
    {
        self::assertTrue(TypeHelper::isScalarType($type));
        self::assertFalse(TypeHelper::isBuiltinClass($type));
        self::assertFalse(TypeHelper::isCustomClass($type));
    }

    /**
     * @covers \JsonMapper\Helpers\TypeHelper
     * @dataProvider builtinTypesDataProvider
     */
    public function testBuiltinTypesAreSeenAsBuiltin(string $type): void
    {
        self::assertTrue(TypeHelper::isBuiltinClass($type));
        self::assertFalse(TypeHelper::isScalarType($type));
        self::assertFalse(TypeHelper::isCustomClass($type));
    }

    /**
     * @covers \JsonMapper\Helpers\TypeHelper
     * @dataProvider castOperationDataProvider
     */
    public function testCastOperationReturnsTheCorrectValue($value, string $castTo, $expected): void
    {
        self::assertEquals($expected, TypeHelper::cast($value, $castTo));
    }

    public function scalarTypesDataProvider(): array
    {
        return [
            'bool' => ['bool'],
            'boolean' => ['boolean'],
            'int' => ['int'],
            'integer' => ['integer'],
            'string' => ['string'],
            'float' => ['float'],
            'double' => ['double'],
        ];
    }

    public function builtinTypesDataProvider(): array
    {
        return [
            \DateTime::class => [\DateTime::class],
            \DateTimeImmutable::class => [\DateTimeImmutable::class],
        ];
    }

    public function castOperationDataProvider(): array
    {
        return [
            'cast to string' => [42, 'string', '42'],
            'cast to boolean true' => [1, 'bool', true],
            'cast to boolean false' => [0, 'bool', false],
            'cast to int' => ['42', 'int', 42],
            'cast to float' => ['34.567', 'float', 34.567],
        ];
    }
}

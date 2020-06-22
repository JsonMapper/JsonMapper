<?php

declare(strict_types=1);

namespace JsonMapper\Tests\ValueObjects\Unit;

use JsonMapper\ValueObjects\PropertyType;
use PHPUnit\Framework\TestCase;

class PropertyTypeTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\PropertyType
     */
    public function testGettersReturnConstructorValues(): void
    {
        $propertyType = new PropertyType('int', false, false);

        self::assertSame('int', $propertyType->getType());
        self::assertFalse($propertyType->isNullable());
        self::assertFalse($propertyType->isArray());
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyType
     */
    public function testCanBeConvertedToJson(): void
    {
        $propertyType = new PropertyType('int', false, false);

        $propertyAsJson = json_encode($propertyType);

        self::assertIsString($propertyAsJson);
        self::assertJsonStringEqualsJsonString(
            '{"type":"int","isNullable":false,"isArray":false}',
            (string) $propertyAsJson
        );
    }
}

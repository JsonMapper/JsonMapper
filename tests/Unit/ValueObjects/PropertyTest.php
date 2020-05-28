<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\ValueObjects;

use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testGettersReturnConstructorValues(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC(), false);

        self::assertSame('id', $property->getName());
        self::assertSame('int', $property->getType());
        self::assertFalse($property->isNullable());
        self::assertTrue($property->getVisibility()->equals(Visibility::PUBLIC()));
        self::assertFalse($property->isArray());
    }

    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testPropertyCanBeConvertedToBuilderAndBack(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC(), true);
        $builder = $property->asBuilder();

        self::assertEquals($property, $builder->build());
    }

    /**
     * @covers \JsonMapper\ValueObjects\Property
     */
    public function testCanBeConvertedToJson(): void
    {
        $property = new Property('id', 'int', false, Visibility::PUBLIC(), true);

        $propertyAsJson = json_encode($property);

        self::assertIsString($propertyAsJson);
        self::assertJsonStringEqualsJsonString(
            '{"name":"id","type":"int","isNullable":false,"visibility":"public","isArray":true}',
            (string) $propertyAsJson
        );
    }
}

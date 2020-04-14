<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\ValueObjects;

use JsonMapper\Enums\Visibility;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use PHPUnit\Framework\TestCase;

class PropertyMapTest extends TestCase
{
    /**
     * @covers \JsonMapper\ValueObjects\PropertyMap
     */
    public function testPropertyCanBeAdded(): void
    {
        $property = new Property('name', 'string', true, Visibility::PUBLIC());
        $map = new PropertyMap();
        $map->addProperty($property);

        self::assertTrue($map->hasProperty('name'));
        self::assertSame($property, $map->getProperty('name'));
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyMap
     */
    public function testGetPropertyThrowsErrorWhenPropertyDoesntExist(): void
    {
        $map = new PropertyMap();

        $this->expectException(\Exception::class);
        $map->getProperty('missing');
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyMap
     */
    public function testMapReturnsCorrectIterator(): void
    {
        $property = new Property('name', 'string', true, Visibility::PUBLIC());
        $map = new PropertyMap();
        $map->addProperty($property);
        $iterator = $map->getIterator();

        self::assertCount(1, $iterator);
        self::assertSame($property, $iterator->current());
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyMap
     */
    public function testCanBeConvertedToJson(): void
    {
        $map = new PropertyMap();
        $map->addProperty(new Property('id', 'int', false, Visibility::PUBLIC()));

        $mapAsJson = json_encode($map);

        self::assertIsString($mapAsJson);
        self::assertJsonStringEqualsJsonString(
            '{"properties":{"id":{"name":"id","type":"int","isNullable":false,"visibility":"public"}}}',
            (string) $mapAsJson
        );
    }

    /**
     * @covers \JsonMapper\ValueObjects\PropertyMap
     */
    public function testCanBeConvertedToString(): void
    {
        $map = new PropertyMap();
        $map->addProperty(new Property('id', 'int', false, Visibility::PUBLIC()));

        $mapAsString = $map->toString();

        self::assertIsString($mapAsString);
        self::assertJsonStringEqualsJsonString(
            '{"properties":{"id":{"name":"id","type":"int","isNullable":false,"visibility":"public"}}}',
            (string) $mapAsString
        );
    }
}

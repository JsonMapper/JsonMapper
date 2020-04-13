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
}

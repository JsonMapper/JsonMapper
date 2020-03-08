<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper;

use DannyVanDerSluijs\JsonMapper\JsonMapper;
use DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations;
use DannyVanDerSluijs\JsonMapper\Strategies\TypedProperties;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\SimpleObject;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Php74\SimpleObject as Php74SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    public function testItCanMapAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    public function testItCanMapAnDateTimeImmutableProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new SimpleObject();
        $json = (object) ['date' => '2020-03-08 12:42:14'];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertEquals(new \DateTimeImmutable('2020-03-08 12:42:14'), $object->date);
    }

    /**
     * @requires PHP >= 7.4
     */
    public function testItCanMapAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = new JsonMapper([new TypedProperties()]);
        $object = new Php74SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }
}

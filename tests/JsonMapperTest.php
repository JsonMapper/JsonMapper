<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper;

use DannyVanDerSluijs\JsonMapper\JsonMapper;
use DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations;
use DannyVanDerSluijs\JsonMapper\Strategies\TypedProperties;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Popo;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Php74\Popo as Php74Popo;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     */
    public function testItCanMapAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     */
    public function testItCanMapAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new SimpleObject();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\DocBlockAnnotations
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     */
    public function testItCanMapAnDateTimeImmutableProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper([new DocBlockAnnotations()]);
        $object = new Popo();
        $json = (object) ['date' => '2020-03-08 12:42:14'];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertEquals(new \DateTimeImmutable('2020-03-08 12:42:14'), $object->date);
    }

    /**
     * @requires PHP >= 7.4
     *
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\TypedProperties
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     */
    public function testItCanMapAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = new JsonMapper([new TypedProperties()]);
        $object = new Php74Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    /**
     * @requires PHP >= 7.4
     *
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Strategies\TypedProperties
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     */
    public function testItAppliesTypeCastingMappingAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = new JsonMapper([new TypedProperties()]);
        $object = new Php74Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }
}

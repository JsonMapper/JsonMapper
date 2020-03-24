<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper;

use DannyVanDerSluijs\JsonMapper\JsonMapper;
use DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper;
use DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations;
use DannyVanDerSluijs\JsonMapper\Middleware\TypedProperties;
use DannyVanDerSluijs\JsonMapper\Middleware\FullQualifiedClassNameResolver;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\ComplexObject;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Popo;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\Php74\Popo as Php74Popo;
use DannyVanDerSluijs\Tests\JsonMapper\Implementation\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $object = new Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $object = new Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $object = new SimpleObject();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnDateTimeImmutableProperty(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
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
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\TypedProperties<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new TypedProperties());
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
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\TypedProperties<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItAppliesTypeCastingMappingAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new TypedProperties());
        $object = new Php74Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\UseStatementHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Parser\UseNodeVisitor
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnObjectWithACustomClassAttribute(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $mapper->push(new FullQualifiedClassNameResolver());
        $object = new ComplexObject();
        $json = (object) ['child' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getChild()->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\UseStatementHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Parser\UseNodeVisitor
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnObjectWithACustomClassAttributeFromAnotherNamespace(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $mapper->push(new FullQualifiedClassNameResolver());
        $object = new ComplexObject();
        $json = (object) ['user' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getUser()->getName());
    }

    /**
     * @covers \DannyVanDerSluijs\JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper
     * @covers \DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper
     * @covers \DannyVanDerSluijs\JsonMapper\JsonMapper
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap
     * @covers \DannyVanDerSluijs\JsonMapper\ValueObjects\Property
     * @covers \DannyVanDerSluijs\JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper
     * @covers \DannyVanDerSluijs\JsonMapper\Handler\PropertyMapper
     */
    public function testItCanMapAnArrayOfObjects(): void
    {
        // Arrange
        $mapper = new JsonMapper(new PropertyMapper());
        $mapper->push(new DocBlockAnnotations());
        $object = new SimpleObject();
        $json = [(object) ['name' => 'one'], (object) ['name' => 'two']];

        // Act
        $result = $mapper->mapArray($json, $object);

        // Assert
        self::assertContainsOnly(SimpleObject::class, $result);
        self::assertSame('one', $result[0]->getName());
        self::assertSame('two', $result[1]->getName());
    }
}

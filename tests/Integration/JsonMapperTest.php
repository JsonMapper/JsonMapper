<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Integration;

use JsonMapper\JsonMapperFactory;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Popo;
use JsonMapper\Tests\Implementation\Php74\Popo as Php74Popo;
use JsonMapper\Tests\Implementation\SimpleObject;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->name);
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new SimpleObject();
        $json = (object) ['name' => __METHOD__];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getName());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItAppliesTypeCastingWhenMappingAnObjectUsingAPublicSetter(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new SimpleObject();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->getName());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnDateTimeImmutableProperty(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
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
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
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
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItAppliesTypeCastingMappingAnObjectWithTypedProperties(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new Php74Popo();
        $json = (object) ['name' => 42];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame('42', $object->name);
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnObjectWithACustomClassAttribute(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $json = (object) ['child' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getChild()->getName());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnObjectWithACustomClassAttributeFromAnotherNamespace(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
        $object = new ComplexObject();
        $json = (object) ['user' => (object) ['name' => __METHOD__]];

        // Act
        $mapper->mapObject($json, $object);

        // Assert
        self::assertSame(__METHOD__, $object->getUser()->getName());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     * @covers \JsonMapper\JsonMapperFactory
     * @covers \JsonMapper\Handler\PropertyMapper
     * @covers \JsonMapper\Middleware\DocBlockAnnotations<extended>
     * @covers \JsonMapper\Middleware\FullQualifiedClassNameResolver<extended>
     * @covers \JsonMapper\Middleware\TypedProperties<extended>
     * @covers \JsonMapper\Parser\UseNodeVisitor
     * @covers \JsonMapper\Builders\PropertyBuilder
     * @covers \JsonMapper\Enums\Visibility::fromReflectionProperty
     * @covers \JsonMapper\Helpers\AnnotationHelper
     * @covers \JsonMapper\Helpers\TypeHelper
     * @covers \JsonMapper\Helpers\UseStatementHelper
     * @covers \JsonMapper\ValueObjects\PropertyMap
     * @covers \JsonMapper\ValueObjects\Property
     * @covers \JsonMapper\Wrapper\ObjectWrapper
     */
    public function testItCanMapAnArrayOfObjects(): void
    {
        // Arrange
        $mapper = (new JsonMapperFactory())->bestFit();
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

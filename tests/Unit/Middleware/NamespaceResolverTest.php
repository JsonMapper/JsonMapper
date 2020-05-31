<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\NamespaceResolver;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Models\User;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class NamespaceResolverTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesForImportedNamespace(): void
    {
        $middleware = new NamespaceResolver();
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('user')
            ->setType('User')
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('user'));
        self::assertEquals(User::class, $propertyMap->getProperty('user')->getType());
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesWithinSameNamespace(): void
    {
        $middleware = new NamespaceResolver();
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('child')
            ->setType('SimpleObject')
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('child'));
        self::assertEquals(SimpleObject::class, $propertyMap->getProperty('child')->getType());
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItDoesntApplyResolvingToScalarTypes(): void
    {
        $middleware = new NamespaceResolver();
        $object = new SimpleObject();
        $property = PropertyBuilder::new()
            ->setName('name')
            ->setType('string')
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        self::assertEquals('string', $propertyMap->getProperty('name')->getType());
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesForImportedNamespaceWithArray(): void
    {
        $middleware = new NamespaceResolver();
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('user')
            ->setType('User')
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->setIsArray(true)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('user'));
        self::assertEquals(User::class, $propertyMap->getProperty('user')->getType());
        self::assertTrue($propertyMap->getProperty('user')->isArray());
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesWithinSameNamespaceWithArray(): void
    {
        $middleware = new NamespaceResolver();
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('child')
            ->setType('SimpleObject[]')
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->setIsArray(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('child'));
        self::assertEquals(SimpleObject::class . '[]', $propertyMap->getProperty('child')->getType());
    }
}

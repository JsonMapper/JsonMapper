<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Cache\NullCache;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\NamespaceResolver;
use JsonMapper\Tests\Helpers\AssertThatPropertyTrait;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\Tests\Implementation\Models\User;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class NamespaceResolverTest extends TestCase
{
    use AssertThatPropertyTrait;

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesForImportedNamespace(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('user')
            ->addType('User', false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('user'));
        $this->assertThatProperty($propertyMap->getProperty('user'))
            ->hasType(User::class, false);
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesWithinSameNamespace(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('child')
            ->addType('SimpleObject', false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('child'));
        $this->assertThatProperty($propertyMap->getProperty('child'))
            ->hasType(SimpleObject::class, false);
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItDoesntApplyResolvingToScalarTypes(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new SimpleObject();
        $property = PropertyBuilder::new()
            ->setName('name')
            ->addType('string', false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        $this->assertThatProperty($propertyMap->getProperty('name'))
            ->hasType('string', false);
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItDoesntApplyResolvingToFullyQualifiedClassName(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new SimpleObject();
        $property = PropertyBuilder::new()
            ->setName('name')
            ->addType(__CLASS__, false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        $this->assertThatProperty($propertyMap->getProperty('name'))
            ->hasType(__CLASS__, false);
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesForImportedNamespaceWithArray(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('user')
            ->addType('User', true)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('user'));
        $this->assertThatProperty($propertyMap->getProperty('user'))
            ->hasType(User::class, true);
    }

    /**
     * @covers \JsonMapper\Middleware\NamespaceResolver
     */
    public function testItResolvesNamespacesWithinSameNamespaceWithArray(): void
    {
        $middleware = new NamespaceResolver(new NullCache());
        $object = new ComplexObject();
        $property = PropertyBuilder::new()
            ->setName('child')
            ->addType('SimpleObject[]', false)
            ->setVisibility(Visibility::PRIVATE())
            ->setIsNullable(false)
            ->build();
        $propertyMap = new PropertyMap();
        $propertyMap->addProperty($property);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('child'));
        $this->assertThatProperty($propertyMap->getProperty('child'))
            ->hasType(SimpleObject::class . '[]', false);
    }
}

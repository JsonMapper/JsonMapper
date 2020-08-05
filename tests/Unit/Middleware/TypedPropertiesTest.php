<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Cache\NullCache;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\TypedProperties;
use JsonMapper\Tests\Implementation\Php74;
use JsonMapper\Tests\Implementation\Php80;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class TypedPropertiesTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\TypedProperties
     * @requires PHP >= 7.4
     * @requires PHP < 8.0
     */
    public function testTypedPropertyIsCorrectlyDiscoveredWithPhp74(): void
    {
        $middleware = new TypedProperties(new NullCache());
        $object = new Php74\Popo();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        self::assertEquals('string', $propertyMap->getProperty('name')->getType());
        self::assertEquals(Visibility::PUBLIC(), $propertyMap->getProperty('name')->getVisibility());
        self::assertFalse($propertyMap->getProperty('name')->isNullable());
    }

    /**
     * @covers \JsonMapper\Middleware\TypedProperties
     * @requires PHP >= 8.0
     */
    public function testTypedPropertyIsCorrectlyDiscoveredWithPhp80AndGreater(): void
    {
        $middleware = new TypedProperties(new NullCache());
        $object = new Php80\Popo();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        self::assertEquals('string', $propertyMap->getProperty('name')->getType());
        self::assertEquals(Visibility::PUBLIC(), $propertyMap->getProperty('name')->getVisibility());
        self::assertFalse($propertyMap->getProperty('name')->isNullable());
        self::assertTrue($propertyMap->hasProperty('mixedParam'));
        self::assertEquals('mixed', $propertyMap->getProperty('mixedParam')->getType());
        self::assertEquals('mixed', $propertyMap->getProperty('mixedParam')->getPropertyType()->getType());
        self::assertEquals(Visibility::PUBLIC(), $propertyMap->getProperty('mixedParam')->getVisibility());
        self::assertTrue($propertyMap->getProperty('mixedParam')->isNullable());
        self::assertFalse($propertyMap->getProperty('mixedParam')->isArray());
    }

    /**
     * @covers \JsonMapper\Middleware\TypedProperties
     * @requires PHP >= 7.4
     */
    public function testDoesntBreakOnMissingTypeDefinition(): void
    {
        $middleware = new TypedProperties(new NullCache());
        $object = new SimpleObject();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertCount(0, $propertyMap);
    }

    /**
     * @covers \JsonMapper\Middleware\TypedProperties
     * @requires PHP >= 7.4
     */
    public function testReturnsFromCacheWhenAvailable(): void
    {
        $propertyMap = new PropertyMap();
        $objectWrapper = $this->createMock(ObjectWrapper::class);
        $objectWrapper->method('getName')->willReturn(__METHOD__);
        $objectWrapper->expects(self::never())->method('getReflectedObject');
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('has')->with(__METHOD__)->willReturn(true);
        $cache->method('get')->with(__METHOD__)->willReturn($propertyMap);
        $middleware = new TypedProperties($cache);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), $objectWrapper, $propertyMap, $jsonMapper);
    }
}

<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Cache\NullCache;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\DocBlockAnnotations;
use JsonMapper\Tests\Helpers\AssertThatPropertyTrait;
use JsonMapper\Tests\Implementation\ComplexObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class DocBlockAnnotationsTest extends TestCase
{
    use AssertThatPropertyTrait;

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testUpdatesThePropertyMap(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new ComplexObject();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('child'));
        self::assertThatProperty($propertyMap->getProperty('child'))
            ->hasType('SimpleObject')
            ->hasVisibility(Visibility::PRIVATE())
            ->isNullable()
            ->isNotArray();
        self::assertTrue($propertyMap->hasProperty('children'));
        self::assertThatProperty($propertyMap->getProperty('children'))
            ->hasType('SimpleObject')
            ->hasVisibility(Visibility::PRIVATE())
            ->isNotNullable()
            ->isArray();
        self::assertTrue($propertyMap->hasProperty('user'));
        self::assertThatProperty($propertyMap->getProperty('user'))
            ->hasType('User')
            ->hasVisibility(Visibility::PRIVATE())
            ->isNotNullable()
            ->isNotArray();
        self::assertTrue($propertyMap->hasProperty('mixedParam'));
        self::assertThatProperty($propertyMap->getProperty('mixedParam'))
            ->hasType('mixed')
            ->hasPropertyType('mixed')
            ->hasVisibility(Visibility::PUBLIC())
            ->isNotNullable()
            ->isNotArray();
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testItCanHandleMissingDocBlock(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            public $number;
        };

        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertEmpty($propertyMap->getIterator());
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testItCanHandleEmptyDocBlock(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            /** */
            public $number;
        };

        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertEmpty($propertyMap->getIterator());
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testItCanHandleIncompleteDocBlock(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            /** @var */
            public $number;
        };

        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertEmpty($propertyMap->getIterator());
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
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
        $middleware = new DocBlockAnnotations($cache);
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), $objectWrapper, $propertyMap, $jsonMapper);
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testTypeIsCorrectlyCalculatedForNullableVars(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            /** @var NullableNumber|null This is a nullable number*/
            public $nullableNumber;
        };
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('nullableNumber'));
        self::assertThatProperty($propertyMap->getProperty('nullableNumber'))
            ->hasType('NullableNumber')
            ->hasVisibility(Visibility::PUBLIC())
            ->isNullable()
            ->isNotArray();
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testTypeIsCorrectlyCalculatedForNullableArray(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            /** @var Number[]|null This is a nullable array number*/
            public $numbers;
        };
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('numbers'));
        self::assertThatProperty($propertyMap->getProperty('numbers'))
            ->hasType('Number')
            ->hasVisibility(Visibility::PUBLIC())
            ->isNullable()
            ->isArray();
    }

    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testTypeIsCorrectlyCalculatedForNullableArrayWhenNullIsProvidedFirst(): void
    {
        $middleware = new DocBlockAnnotations(new NullCache());
        $object = new class {
            /** @var null|Number[] This is a nullable array number*/
            public $numbers;
        };
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('numbers'));
        self::assertThatProperty($propertyMap->getProperty('numbers'))
            ->hasType('Number')
            ->hasVisibility(Visibility::PUBLIC())
            ->isNullable()
            ->isArray();
    }
}

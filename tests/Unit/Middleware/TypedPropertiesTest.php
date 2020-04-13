<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\TypedProperties;
use JsonMapper\Tests\Implementation\Php74\Popo;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class TypedPropertiesTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\TypedProperties
     * @requires PHP >= 7.4
     */
    public function testTypedPropertyIsCorrectlyDiscovered(): void
    {
        $middleware = new TypedProperties();
        $object = new Popo();
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
     * @requires PHP >= 7.4
     */
    public function testDoesntBreakOnMissingTypeDefinition(): void
    {
        $middleware = new TypedProperties();
        $object = new SimpleObject();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertCount(0, $propertyMap);
    }
}

<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit\Middleware;

use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\DocBlockAnnotations;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class DocBlockAnnotationsTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\DocBlockAnnotations
     */
    public function testHandlesMethod(): void
    {
        $middleware = new DocBlockAnnotations();
        $object = new SimpleObject();
        $propertyMap = new PropertyMap();
        $jsonMapper = $this->createMock(JsonMapperInterface::class);

        $middleware->handle(new \stdClass(), new ObjectWrapper($object), $propertyMap, $jsonMapper);

        self::assertTrue($propertyMap->hasProperty('name'));
        self::assertEquals('string', $propertyMap->getProperty('name')->getType());
        self::assertEquals(Visibility::PRIVATE(), $propertyMap->getProperty('name')->getVisibility());
        self::assertFalse($propertyMap->getProperty('name')->isNullable());
    }
}

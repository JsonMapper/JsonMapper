<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit;

use JsonMapper\Dto\NamedMiddleware;
use JsonMapper\Exception\BuilderException;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\Middleware\Attributes\Attributes;
use JsonMapper\Middleware\DocBlockAnnotations;
use JsonMapper\Middleware\NamespaceResolver;
use JsonMapper\Middleware\TypedProperties;
use PHPUnit\Framework\TestCase;

class JsonMapperBuilderTest extends TestCase
{
    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testCanReturnFreshInstance():void
    {
        $instance = JsonMapperBuilder::new();
        $otherInstance = JsonMapperBuilder::new();

        self::assertNotSame($instance, $otherInstance);
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testThrowsExceptionWhenBuildingWithoutMiddleware(): void
    {
        $this->expectException(BuilderException::class);

        JsonMapperBuilder::new()->build();
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithCustomJsonMapperClassName(): void
    {
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withDocBlockAnnotationsMiddleware()
            ->build();

        self::assertInstanceOf(\JsonMapper\Tests\Implementation\JsonMapper::class, $instance);
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testThrowsExceptionSettingJsonMapperClassNameForClassWithoutJsonMapperInterfaceImplementation(): void
    {
        $this->expectException(BuilderException::class);

        JsonMapperBuilder::new()->withJsonMapperClassName(\stdClass::class);
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithCustomPropertyMapper(): void
    {
        $propertyMapper = new PropertyMapper();
        /** @var \JsonMapper\Tests\Implementation\JsonMapper $instance */
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withPropertyMapper($propertyMapper)
            ->withDocBlockAnnotationsMiddleware()
            ->build();

        self::assertSame($propertyMapper, $instance->handler);
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithNamespaceResolverMiddleware(): void
    {
        /** @var \JsonMapper\Tests\Implementation\JsonMapper $instance */
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withNamespaceResolverMiddleware()
            ->build();

        self::assertCount(1, array_filter($instance->stack, static function(NamedMiddleware $middleware): bool {
            return $middleware->getMiddleware() instanceof NamespaceResolver;
        }));
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithDocBlockAnnotationsMiddleware(): void
    {
        /** @var \JsonMapper\Tests\Implementation\JsonMapper $instance */
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withDocBlockAnnotationsMiddleware()
            ->build();

        self::assertCount(1, array_filter($instance->stack, static function(NamedMiddleware $middleware): bool {
            return $middleware->getMiddleware() instanceof DocBlockAnnotations;
        }));
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithTypedPropertiesMiddleware(): void
    {
        /** @var \JsonMapper\Tests\Implementation\JsonMapper $instance */
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withTypedPropertiesMiddleware()
            ->build();

        self::assertCount(1, array_filter($instance->stack, static function(NamedMiddleware $middleware): bool {
            return $middleware->getMiddleware() instanceof TypedProperties;
        }));
    }

    /** @covers \JsonMapper\JsonMapperBuilder */
    public function testItCanBuildWithAttributesMiddleware(): void
    {
        /** @var \JsonMapper\Tests\Implementation\JsonMapper $instance */
        $instance = JsonMapperBuilder::new()
            ->withJsonMapperClassName(\JsonMapper\Tests\Implementation\JsonMapper::class)
            ->withAttributesMiddleware()
            ->build();

        self::assertCount(1, array_filter($instance->stack, static function(NamedMiddleware $middleware): bool {
            return $middleware->getMiddleware() instanceof Attributes;
        }));
    }
}

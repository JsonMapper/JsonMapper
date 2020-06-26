<?php

declare(strict_types=1);

namespace JsonMapper\Tests\Unit;

use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\AbstractMiddleware;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    /** @var callable */
    private $handler;
    /** @var AbstractMiddleware */
    private $middleware;

    protected function setUp(): void
    {
        $this->handler = new class {
            /** @var bool */
            private $called = false;

            public function __invoke(): void
            {
                $this->called = true;
            }

            public function isCalled(): bool
            {
                return $this->called;
            }
        };

        $this->middleware = new class extends AbstractMiddleware{
            /** @var bool */
            private $called = false;

            public function isCalled(): bool
            {
                return $this->called;
            }

            public function handle(
                \stdClass $json,
                ObjectWrapper $object,
                PropertyMap $propertyMap,
                JsonMapperInterface $mapper
            ): void {
                $this->called = true;
            }
        };
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testHandlerFromConstructorIsInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper($this->handler);

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertTrue($this->handler->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testHandlerFromConstructorIsInvokedWhenMappingArray(): void
    {
        $jsonMapper = new JsonMapper($this->handler);

        $jsonMapper->mapArray([new \stdClass()], new \stdClass());

        self::assertTrue($this->handler->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testPushedMiddlewareIsInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->push($this->middleware);

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertTrue($this->middleware->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testPushedMiddlewareIsInvokedWhenMappingArray(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->push($this->middleware);

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertTrue($this->middleware->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testRemovedMiddlewareIsNotInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->push($this->middleware);
        $jsonMapper->remove($this->middleware);

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertFalse($this->middleware->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testRemovedByNameMiddlewareIsNotInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->push($this->middleware, __METHOD__);
        $jsonMapper->removeByName(__METHOD__);

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertFalse($this->middleware->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testShiftedMiddlewareIsNotInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->unshift($this->middleware, __METHOD__);
        $jsonMapper->shift();

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertFalse($this->middleware->isCalled());
    }

    /**
     * @covers \JsonMapper\JsonMapper
     */
    public function testPoppedMiddlewareIsNotInvokedWhenMappingObject(): void
    {
        $jsonMapper = new JsonMapper(new PropertyMapper());
        $jsonMapper->push($this->middleware, __METHOD__);
        $jsonMapper->pop();

        $jsonMapper->mapObject(new \stdClass(), new \stdClass());

        self::assertFalse($this->middleware->isCalled());
    }
}

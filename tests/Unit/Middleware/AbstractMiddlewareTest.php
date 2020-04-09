<?php

declare(strict_types=1);

namespace Middleware;

use JsonMapper\JsonMapperInterface;
use JsonMapper\Middleware\AbstractMiddleware;
use JsonMapper\Tests\Implementation\SimpleObject;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use PHPUnit\Framework\TestCase;

class AbstractMiddlewareTest extends TestCase
{
    /**
     * @covers \JsonMapper\Middleware\AbstractMiddleware
     */
    public function testAbstractMiddlewareInvokesHandleMethod(): void
    {
        $middleware = new class extends AbstractMiddleware
        {
            private $called = false;

            public function isCalled(): bool
            {
                return $this->called;
            }

            public function handle(
                \stdClass $json,
                ObjectWrapper $object,
                PropertyMap $map,
                JsonMapperInterface $mapper
            ): void {
                $this->called = true;
            }
        };
        $json = new \stdClass();
        $wrappedObject = new ObjectWrapper(new SimpleObject());
        $propertyMap = new PropertyMap();
        $mapper = $this->createMock(JsonMapperInterface::class);
        $fn = $middleware->__invoke(static function () {
        });

        $fn($json, $wrappedObject, $propertyMap, $mapper);

        self::assertTrue($middleware->isCalled());
    }
}

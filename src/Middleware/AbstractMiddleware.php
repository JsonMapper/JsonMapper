<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Middleware;

use DannyVanDerSluijs\JsonMapper\JsonMapperInterface;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;
use DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper;

abstract class AbstractMiddleware implements MiddlewareInterface, MiddlewareLogicInterface
{
    public function __invoke(callable $handler): callable
    {
        return function (
            \stdClass $json,
            ObjectWrapper $object,
            PropertyMap $map,
            JsonMapperInterface $mapper
        ) use (
            $handler
        ) {
            $this->handle($json, $object, $map, $mapper);

            $handler($json, $object, $map, $mapper);
        };
    }

    abstract public function handle(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $map,
        JsonMapperInterface $mapper
    ): void;
}

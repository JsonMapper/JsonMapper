<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Middleware;

use DannyVanDerSluijs\JsonMapper\JsonMapperInterface;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;
use DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper;

interface MiddlewareLogicInterface
{
    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $map, JsonMapperInterface $mapper): void;
}

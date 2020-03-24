<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Middleware;

interface MiddlewareInterface
{
    public function __invoke(callable $handler): callable;
}

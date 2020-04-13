<?php

declare(strict_types=1);

namespace JsonMapper;

interface JsonMapperInterface
{
    public function push(callable $middleware, string $name = ''): self;

    public function mapObject(\stdClass $json, object $object): void;

    public function mapArray(array $json, object $object): array;
}

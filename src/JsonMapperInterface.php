<?php

declare(strict_types=1);

namespace JsonMapper;

interface JsonMapperInterface
{
    public function mapObject(\stdClass $json, object $object): void;

    public function mapArray(array $json, object $object): array;
}

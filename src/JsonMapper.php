<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper;

class JsonMapper implements JsonMapperInterface
{
    /** @var JsonMapperInterface[] */
    private $strategies;

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function mapObject(\stdClass $json, object $object): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->mapObject($json, $object);
        }
    }
}

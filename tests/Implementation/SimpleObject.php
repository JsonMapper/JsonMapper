<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper\Implementation;

class SimpleObject
{
    /** @var string */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

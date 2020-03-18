<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\Tests\JsonMapper\Implementation;

class ComplexObject
{
    /** @var SimpleObject */
    private $child;

    public function getChild(): SimpleObject
    {
        return $this->child;
    }

    public function setChild(SimpleObject $child): void
    {
        $this->child = $child;
    }
}

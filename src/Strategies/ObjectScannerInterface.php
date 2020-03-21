<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Strategies;

use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;

interface ObjectScannerInterface
{
    public function scan(object $object): PropertyMap;
}

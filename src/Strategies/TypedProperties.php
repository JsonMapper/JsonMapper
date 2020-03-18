<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Strategies;

use DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder;
use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;

class TypedProperties implements ObjectScannerInterface
{
    public function scan(object $object): PropertyMap
    {
        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties();

        $map = new PropertyMap();
        foreach ($properties as $property) {
            $name = $property->getName();

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($property->getType()->getName())
                ->setIsNullable($property->getType()->allowsNull())
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->build();
            $map->addProperty($property);
        }

        return $map;
    }
}

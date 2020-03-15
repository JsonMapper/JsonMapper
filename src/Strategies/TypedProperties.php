<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Strategies;

use DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder;
use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper;
use DannyVanDerSluijs\JsonMapper\JsonMapperInterface;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;

class TypedProperties implements JsonMapperInterface
{
    public function mapObject(\stdClass $json, object $object): void
    {
        $propertyMap = $this->reflect($object);
        foreach ($json as $key => $value) {
            if (! $propertyMap->hasProperty($key)) {
                continue;
            }

            $propertyInfo = $propertyMap->getProperty($key);
            $type = $propertyInfo->getType();

            if (TypeHelper::isScalarType($type)) {
                $value = TypeHelper::cast($value, $type);
            }

            $object->$key = $value;
            continue;
        }
    }

    private function reflect(object $object): PropertyMap
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
                ->setVisibility(self::getVisibility($property))
                ->build();
            $map->addProperty($property);
        }

        return $map;
    }

    private static function getVisibility(\ReflectionProperty $property): Visibility
    {
        if ($property->isPublic()) {
            return Visibility::PUBLIC();
        }
        if ($property->isProtected()) {
            return Visibility::PROTECTED();
        }
        return Visibility::PRIVATE();
    }
}

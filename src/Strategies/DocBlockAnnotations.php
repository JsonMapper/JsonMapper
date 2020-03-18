<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Strategies;

use DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder;
use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper;
use DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;

class DocBlockAnnotations implements ObjectScannerInterface
{
    public function scan(object $object): PropertyMap
    {
        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties();

        $map = new PropertyMap();
        foreach ($properties as $property) {
            $name = $property->getName();
            $annotations = AnnotationHelper::parseAnnotations((string) $property->getDocComment());
            $type = $annotations['var'][0];
            if (TypeHelper::isCustomClass($type)) {
                $type = $reflectionClass->getNamespaceName() . '\\' . $type;
            }

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($type)
                ->setIsNullable(AnnotationHelper::isNullable($annotations['var'][0]))
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->build();
            $map->addProperty($property);
        }

        return $map;
    }
}

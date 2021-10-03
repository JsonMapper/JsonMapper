<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class IgnorePropertiesVisibility extends AbstractMiddleware
{
    public function handle(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        foreach ($object->getReflectedObject()->getProperties() as $reflectionProperty) {
            $property = PropertyBuilder::new()
                ->setName($reflectionProperty->getName())
                ->setIsNullable(false)
                ->setVisibility(Visibility::IGNORE())
                ->build();

            $propertyMap->addProperty($property);
        }
    }
}

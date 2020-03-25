<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class TypedProperties extends AbstractMiddleware
{
    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $map, JsonMapperInterface $mapper): void
    {
        $reflectionProperties = $object->getReflectedObject()->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            $type = $reflectionProperty->getType();

            if ($type === null) {
                continue;
            }

            $reflectionProperty = PropertyBuilder::new()
                ->setName($reflectionProperty->getName())
                ->setType($type->getName())
                ->setIsNullable($type->allowsNull())
                ->setVisibility(Visibility::fromReflectionProperty($reflectionProperty))
                ->build();
            $map->addProperty($reflectionProperty);
        }
    }
}

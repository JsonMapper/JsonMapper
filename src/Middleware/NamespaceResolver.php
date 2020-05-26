<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Helpers\TypeHelper;
use JsonMapper\Helpers\UseStatementHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class NamespaceResolver extends AbstractMiddleware
{
    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $propertyMap, JsonMapperInterface $mapper): void
    {
        $imports = UseStatementHelper::getImports($object->getReflectedObject());

        /** @var Property $property */
        foreach ($propertyMap as &$property) {
            $type = $property->getType();
            if (TypeHelper::isArray($type, $innerType)) {
                $type = $innerType;
            }

            if (! TypeHelper::isCustomClass($type)) {
                continue;
            }

            $matches = array_filter(
                $imports,
                static function (string $import) use ($property) {
                    return $property->getType() === substr($import, -1 * strlen($property->getType()));
                }
            );

            if (count($matches) > 0) {
                $type = array_shift($matches);
                $propertyMap->addProperty($property->asBuilder()->setType($type)->build());
                continue;
            }

            $type = $object->getReflectedObject()->getNamespaceName() . '\\' . $property->getType();
            $propertyMap->addProperty($property->asBuilder()->setType($type)->build());
        }
    }
}

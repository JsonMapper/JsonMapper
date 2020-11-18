<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Helpers\ClassHelper;
use JsonMapper\Helpers\UseStatementHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;

class NamespaceResolver extends AbstractMiddleware
{
    public function handle(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        $imports = UseStatementHelper::getImports($object->getReflectedObject());

        /** @var Property $property */
        foreach ($propertyMap as &$property) {
            if (ScalarType::isValid($property->getPropertyType()->getType()) || ClassHelper::isBuiltin($property->getPropertyType()->getType())) {
                continue;
            }

            $matches = array_filter(
                $imports,
                static function (string $import) use ($property) {
                    return $property->getPropertyType()->getType() === substr($import, -1 * strlen($property->getPropertyType()->getType()));
                }
            );

            if (count($matches) > 0) {
                $type = array_shift($matches);
                $propertyMap->addProperty($property->asBuilder()->setType($type)->build());
                continue;
            }

            if (!class_exists($property->getPropertyType()->getType())) {
                $type = $object->getReflectedObject()->getNamespaceName() . '\\' . $property->getPropertyType()->getType();
                $propertyMap->addProperty($property->asBuilder()->setType($type)->build());
            }
        }
    }
}

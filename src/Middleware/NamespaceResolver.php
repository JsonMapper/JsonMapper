<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Enums\ScalarType;
use JsonMapper\Helpers\ClassHelper;
use JsonMapper\Helpers\UseStatementHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\Property;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\ValueObjects\PropertyType;
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
        foreach ($propertyMap as $property) {
            $types = $property->getPropertyTypes();
            foreach ($types as $index => $type) {
                $types[$index] = $this->resolveSingleType($type, $object, $imports);
            }
            $propertyMap->addProperty($property->asBuilder()->setTypes(...$types)->build());
        }
    }

    private function resolveSingleType(PropertyType $type, ObjectWrapper $object, $imports): PropertyType
    {
        if (ScalarType::isValid($type->getType()) || ClassHelper::isBuiltin($type->getType())) {
            return $type;
        }

        $matches = array_filter(
            $imports,
            static function (string $import) use ($type) {
                return $type->getType() === substr($import, -1 * strlen($type->getType()));
            }
        );

        if (count($matches) > 0) {
            return new PropertyType(array_shift($matches), $type->isArray());
        }

        if (!class_exists($type->getType())) {
            return new PropertyType($object->getReflectedObject()->getNamespaceName() . '\\' . $type->getType(), $type->isArray());
        }

        return $type;
    }
}

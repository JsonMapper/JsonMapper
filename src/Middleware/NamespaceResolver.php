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
    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $map, JsonMapperInterface $mapper): void
    {
        $imports = UseStatementHelper::getImports($object->getReflectedObject());

        /** @var Property $property */
        foreach ($map as &$property) {
            if (! TypeHelper::isCustomClass($property->getType())) {
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
                $map->addProperty($property->asBuilder()->setType($type)->build());
                continue;
            }

            $type = $object->getReflectedObject()->getNamespaceName() . '\\' . $property->getType();
            $map->addProperty($property->asBuilder()->setType($type)->build());
        }
    }
}

<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Middleware;

use DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper;
use DannyVanDerSluijs\JsonMapper\Helpers\UseStatementHelper;
use DannyVanDerSluijs\JsonMapper\JsonMapperInterface;
use DannyVanDerSluijs\JsonMapper\ValueObjects\Property;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;
use DannyVanDerSluijs\JsonMapper\Wrapper\ObjectWrapper;

class FullQualifiedClassNameResolver extends AbstractMiddleware
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

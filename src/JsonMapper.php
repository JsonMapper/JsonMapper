<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper;

use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper;
use DannyVanDerSluijs\JsonMapper\Strategies\ObjectScannerInterface;

class JsonMapper implements JsonMapperInterface
{
    /** @var ObjectScannerInterface */
    private $objectScanner;

    public function __construct(ObjectScannerInterface $objectScanner)
    {
        $this->objectScanner = $objectScanner;
    }

    public function mapObject(\stdClass $json, object $object): void
    {
        $propertyMap = $this->objectScanner->scan($object);

        foreach ($json as $key => $value) {
            if (! $propertyMap->hasProperty($key)) {
                continue;
            }

            $propertyInfo = $propertyMap->getProperty($key);
            $type = $propertyInfo->getType();

            if (TypeHelper::isBuiltinClass($type)) {
                $value = new $type($value);
            }
            if (TypeHelper::isScalarType($type)) {
                $value = TypeHelper::cast($value, $type);
            }
            if (TypeHelper::isCustomClass($type)) {
                $instance = new $type();
                $this->mapObject($value, $instance);
                $value = $instance;
            }

            if ($propertyInfo->getVisibility()->equals(Visibility::PUBLIC())) {
                $object->$key = $value;
                continue;
            }

            $setterMethod = 'set' . ucfirst($key);
            if (method_exists($object, $setterMethod)) {
                $object->$setterMethod($value);
            }
        }
    }

    public function mapArray(\stdClass $json, object $object): array
    {
        $results = [];
        foreach ($json as $key => $value) {
            $results[$key] = clone $object;
            $this->mapObject($value, $results[$key]);
        }

        return $results;
    }
}

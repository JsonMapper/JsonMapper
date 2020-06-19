<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Helpers\AnnotationHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\ValueObjects\PropertyType;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

class DocBlockAnnotations extends AbstractMiddleware
{


    /** @var CacheInterface */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function handle(
        \stdClass $json,
        ObjectWrapper $object,
        PropertyMap $propertyMap,
        JsonMapperInterface $mapper
    ): void {
        $propertyMap->merge($this->fetchPropertyMapForObject($object));
    }

    private function fetchPropertyMapForObject(ObjectWrapper $object): PropertyMap
    {
        if ($this->cache->has($object->getName())) {
            return $this->cache->get($object->getName());
        }

        $properties = $object->getReflectedObject()->getProperties();
        $intermediatePropertyMap = new PropertyMap();

        foreach ($properties as $property) {
            $name = $property->getName();
            $propertyDetails = $this->derivePropertyDetailsFromReflectionProperty($property);

            if (is_null($propertyDetails)) {
                continue;
            }

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($propertyDetails->getType())
                ->setIsNullable($propertyDetails->isNullable())
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->setIsArray($propertyDetails->isArray())
                ->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($object->getName(), $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }

    private function derivePropertyDetailsFromReflectionProperty(\ReflectionProperty $property): ?PropertyType
    {
        $docBlock = $property->getDocComment();
        if ($docBlock === false) {
            return null;
        }

        $annotations = AnnotationMap::fromDocBlock($docBlock);

        if (! $annotations->hasVar()) {
            return null;
        }

        $type = $annotations->getVar();

        $isArray = substr($type, -2) === '[]';
        if ($isArray) {
            $type = substr($type, 0, -2);
        }

        $nullable = stripos('|' . $type . '|', '|null|') !== false;

        return new PropertyType($type, $nullable, $isArray);
    }
}

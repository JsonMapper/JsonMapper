<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Helpers\AnnotationHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\PropertyMap;
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

    public function handle(\stdClass $json, ObjectWrapper $object, PropertyMap $propertyMap, JsonMapperInterface $mapper): void
    {
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
            $docblock = $property->getDocComment();

            if ($docblock === false) {
                continue;
            }

            $annotations = AnnotationHelper::parseAnnotations($docblock);
            $type = $annotations['var'][0];

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($type)
                ->setIsNullable(AnnotationHelper::isNullable($annotations['var'][0]))
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($object->getName(), $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }
}

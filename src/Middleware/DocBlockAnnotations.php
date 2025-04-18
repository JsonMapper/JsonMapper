<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\Helpers\DocBlockHelper;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

class DocBlockAnnotations extends AbstractMiddleware
{
    private const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>(?:[\w\[\]\\\\|<>]+(?:,\s*)?)*).*$/m';

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
        $cacheKey = \sprintf(
            '%sCache%s',
            str_replace(['{', '}', '(', ')', '/', '\\', '@', ':' ], '', __CLASS__),
            str_replace(['{', '}', '(', ')', '/', '\\', '@', ':' ], '', $object->getName())
        );
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $intermediatePropertyMap = new PropertyMap();
        foreach ($this->getObjectPropertiesIncludingParents($object) as $property) {
            $name = $property->getName();
            $docBlock = $property->getDocComment();
            if ($docBlock === false) {
                continue;
            }

            $annotations = DocBlockHelper::parseDocBlockToAnnotationMap($docBlock);

            if (! $annotations->hasVar()) {
                continue;
            }

            $types = \explode('|', $annotations->getVar());
            $nullable = \in_array('null', $types, true);
            $types = \array_filter($types, static function (string $type) {
                return $type !== 'null';
            });

            $builder = PropertyBuilder::new()
                ->setName($name)
                ->setIsNullable($nullable)
                ->setVisibility(Visibility::fromReflectionProperty($property));

            /* A union type that has one of its types defined as array is to complex to understand */
            if (\in_array('array', $types, true)) {
                $property = $builder->addType('mixed', ArrayInformation::singleDimension())->build();
                $intermediatePropertyMap->addProperty($property);
                continue;
            }

            foreach ($types as $type) {
                $type = \trim($type);
                $isAnArrayType = $this->isArrayType($type);

                if (! $isAnArrayType) {
                    $builder->addType($type, ArrayInformation::notAnArray());
                    continue;
                }

                $arrayInformation = $this->determineArrayInformation($type);
                $builder->addType($type, $arrayInformation);
            }

            $property = $builder->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($cacheKey, $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }

    /** @return \ReflectionProperty[] */
    private function getObjectPropertiesIncludingParents(ObjectWrapper $object): array
    {
        $properties = [];
        $reflectionClass = $object->getReflectedObject();
        do {
            $properties = array_merge($properties, $reflectionClass->getProperties());
        } while ($reflectionClass = $reflectionClass->getParentClass());
        return $properties;
    }

    private function isArrayType(string $type): bool
    {
        return \substr($type, -2) === '[]'
            || \strpos($type, 'list<') === 0
            || \strpos($type, 'array<') === 0;
    }

    private function determineArrayInformation(string &$type): ArrayInformation
    {
        $levels = 0;
        while (true) {
            if (substr($type, -2) === '[]') {
                $levels++;
                $type = \substr($type, 0, -2);

                continue;
            }

            if (strpos($type, 'list<') === 0) {
                $levels++;
                $type = \substr($type, 5, -1);

                continue;
            }

            if (strpos($type, 'array<') === 0) {
                $levels++;
                $offset = 6;
                $commaPosition = strpos($type, ',');
                if (is_int($commaPosition)) {
                    $offset = $commaPosition + 1;
                }
                $type = \trim(\substr($type, $offset, -1));

                continue;
            }

            break;
        }

        return $levels === 0 ? ArrayInformation::notAnArray() : ArrayInformation::multiDimension($levels);
    }
}

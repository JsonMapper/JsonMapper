<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

class DocBlockAnnotations extends AbstractMiddleware
{
    private const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>[\w\[\]\\\\|]*).*$/m';

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

            $annotations = self::parseDocBlockToAnnotationMap($docBlock);

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
                $isAnArrayType = \substr($type, -2) === '[]';

                if (! $isAnArrayType) {
                    $builder->addType($type, ArrayInformation::notAnArray());
                    continue;
                }

                $initialBracketPosition = strpos($type, '[');
                $dimensions = substr_count($type, '[]');

                if ($initialBracketPosition !== false) {
                    $type = substr($type, 0, $initialBracketPosition);
                }

                $builder->addType($type, ArrayInformation::multiDimension($dimensions));
            }

            $property = $builder->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($cacheKey, $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }

    public static function parseDocBlockToAnnotationMap(string $docBlock): AnnotationMap
    {
        // Strip away the start "/**' and ending "*/"
        if (strpos($docBlock, '/**') === 0) {
            $docBlock = \substr($docBlock, 3);
        }
        if (substr($docBlock, -2) === '*/') {
            $docBlock = \substr($docBlock, 0, -2);
        }
        $docBlock = \trim($docBlock);

        $var = null;
        if (\preg_match_all(self::DOC_BLOCK_REGEX, $docBlock, $matches)) {
            for ($x = 0, $max = count($matches[0]); $x < $max; $x++) {
                if ($matches['name'][$x] === 'var') {
                    $var = $matches['value'][$x];
                }
            }
        }

        return new AnnotationMap($var ?: null, [], null);
    }

    /** @return \ReflectionProperty[] */
    public function getObjectPropertiesIncludingParents(ObjectWrapper $object): array
    {
        $properties = [];
        $reflectionClass = $object->getReflectedObject();
        do {
            $properties = array_merge($properties, $reflectionClass->getProperties());
        } while ($reflectionClass = $reflectionClass->getParentClass());
        return $properties;
    }
}

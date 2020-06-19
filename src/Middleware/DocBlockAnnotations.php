<?php

declare(strict_types=1);

namespace JsonMapper\Middleware;

use JsonMapper\Builders\PropertyBuilder;
use JsonMapper\Enums\Visibility;
use JsonMapper\JsonMapperInterface;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\PropertyMap;
use JsonMapper\ValueObjects\PropertyType;
use JsonMapper\Wrapper\ObjectWrapper;
use Psr\SimpleCache\CacheInterface;

class DocBlockAnnotations extends AbstractMiddleware
{
    private const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

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

        $annotations = self::parseDocBlockToAnnotationMap($docBlock);

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

    public static function parseDocBlockToAnnotationMap(string $docBlock): AnnotationMap
    {
        // Strip away the start "/**' and ending "*/"
        if (strpos($docBlock, '/**') === 0) {
            $docBlock = substr($docBlock, 3);
        }
        if (substr($docBlock, -2) === '*/') {
            $docBlock = substr($docBlock, 0, -2);
        }
        $docBlock = trim($docBlock);

        if (preg_match_all(self::DOC_BLOCK_REGEX, $docBlock, $matches)) {
            for ($x = 0, $max = count($matches[0]); $x < $max; $x++) {
                switch ($matches['name'][$x]) {
                    case 'var':
                        $var = $matches['value'][$x];
                        break;
                    case 'param':
                        $params = $matches['value'];
                        break;
                    case 'return':
                        $return = $matches['value'][$x];
                        break;
                }
            }
        }

        return new AnnotationMap($var ?? null, $params ?? [], $return ?? null);
    }
}

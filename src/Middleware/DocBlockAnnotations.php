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
    private const ANNOTATION_REGEX = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';

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
            $docblock = $property->getDocComment();

            if ($docblock === false) {
                continue;
            }

            [$type, $nullable, $isArray] = $this->parsePropertyDocBlock($docblock);
            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($type)
                ->setIsNullable($nullable)
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->setIsArray($isArray)
                ->build();
            $intermediatePropertyMap->addProperty($property);
        }

        $this->cache->set($object->getName(), $intermediatePropertyMap);

        return $intermediatePropertyMap;
    }

    private function parsePropertyDocBlock(string $docBlock): array
    {
        $annotations = $this->parseAnnotations($docBlock);
        $type = $annotations['var'][0];
        $isArray = substr($type, -2) === '[]';
        if ($isArray) {
            $type = substr($type, 0, -2);
        }

        $nullable = stripos('|' . $type . '|', '|null|') !== false;

        return [$type, $nullable, $isArray];
    }

    private function parseAnnotations(string $docBlock): array
    {
        // Strip away the start "/**' and ending "*/"
        if (strpos($docBlock, '/**') === 0) {
            $docBlock = substr($docBlock, 3);
        }
        if (substr($docBlock, -2) === '*/') {
            $docBlock = substr($docBlock, 0, -2);
        }

        $annotations = [];
        if (preg_match_all(self::ANNOTATION_REGEX, $docBlock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
    }
}

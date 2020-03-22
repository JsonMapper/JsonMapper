<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Strategies;

use DannyVanDerSluijs\JsonMapper\Builders\PropertyBuilder;
use DannyVanDerSluijs\JsonMapper\Enums\Visibility;
use DannyVanDerSluijs\JsonMapper\Helpers\AnnotationHelper;
use DannyVanDerSluijs\JsonMapper\Helpers\TypeHelper;
use DannyVanDerSluijs\JsonMapper\Helpers\UseStatementHelper;
use DannyVanDerSluijs\JsonMapper\ValueObjects\PropertyMap;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;

class DocBlockAnnotations implements ObjectScannerInterface
{
    public function scan(object $object): PropertyMap
    {
        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties();

        $map = new PropertyMap();
        foreach ($properties as $property) {
            $name = $property->getName();
            $annotations = AnnotationHelper::parseAnnotations((string) $property->getDocComment());
            $type = $annotations['var'][0];
            if (TypeHelper::isCustomClass($type)) {
                $type = $this->resolveToFullyQualifiedClassName($type, $reflectionClass);
            }

            $property = PropertyBuilder::new()
                ->setName($name)
                ->setType($type)
                ->setIsNullable(AnnotationHelper::isNullable($annotations['var'][0]))
                ->setVisibility(Visibility::fromReflectionProperty($property))
                ->build();
            $map->addProperty($property);
        }

        return $map;
    }

    private function resolveToFullyQualifiedClassName(string $type, \ReflectionClass $reflectionClass): string
    {
        $imports = array_filter(
            UseStatementHelper::getImports($reflectionClass),
            static function (string $import) use ($type) {
                return $type === substr($import, -1 * strlen($type));
            }
        );

        if (count($imports) > 0) {
            return $imports[0];
        }

        return $reflectionClass->getNamespaceName() . '\\' . $type;
    }
}

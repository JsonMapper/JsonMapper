<?php

declare(strict_types=1);

namespace JsonMapper\Helpers;

use JsonMapper\Parser\Import;
use JsonMapper\ValueObjects\AnnotationMap;
use JsonMapper\ValueObjects\ArrayInformation;
use JsonMapper\ValueObjects\PropertyType;

class DocBlockHelper
{
    private const PATTERN = '/@(?P<annotation>[A-Za-z_-]+)[ \t]+(?P<type>\??(?:[\w\[\]\\\\|<>]+(?:,\s*)?)*)[ \t]*\$?(?P<name>[\w\[\]\\\\|]*)/m';

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
        $params = [];
        if (\preg_match_all(self::PATTERN, $docBlock, $matches)) {
            for ($x = 0, $max = count($matches[0]); $x < $max; $x++) {
                if ($matches['annotation'][$x] === 'var') {
                    $var = $matches['type'][$x];
                }
                if ($matches['annotation'][$x] === 'param') {
                    $params[$matches['name'][$x]] = $matches['type'][$x];
                }
            }
        }

        return new AnnotationMap($var ?: null, $params, null);
    }

    /**
     * @param Import[] $imports
     * @return PropertyType[]
     */
    public static function deriveTypesFromDocBlockType(string $docBlockType, \ReflectionClass $class, array $imports): array
    {
        $types = [];

        if (strpos($docBlockType, '?') === 0) {
            $docBlockType = \substr($docBlockType, 1);
        }

        $docBlockTypes = \explode('|', $docBlockType);
        $docBlockTypes = \array_filter($docBlockTypes, static function (string $docBlockType) {
            return $docBlockType !== 'null';
        });

        foreach ($docBlockTypes as $dt) {
            $dt = \trim($dt);
            $isAnArrayType = self::isArrayType($dt);

            if (! $isAnArrayType) {
                $type = NamespaceHelper::resolveNamespace($dt, $class->getNamespaceName(), $imports);
                $types[] = new PropertyType($type, ArrayInformation::notAnArray());
                continue;
            }

            $arrayInformation = self::determineArrayInformation($dt);

            $type = NamespaceHelper::resolveNamespace(
                $dt,
                $class->getNamespaceName(),
                $imports
            );

            $types[] = new PropertyType($type, $arrayInformation);
        }

        return $types;
    }

    private static function isArrayType(string $type): bool
    {
        return \substr($type, -2) === '[]'
            || \strpos($type, 'list<') === 0
            || \strpos($type, 'array<') === 0;
    }

    private static function determineArrayInformation(string &$type): ArrayInformation
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

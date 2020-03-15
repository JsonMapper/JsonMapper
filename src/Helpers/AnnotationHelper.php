<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Helpers;

class AnnotationHelper
{
    public static function isNullable(?string $type): bool
    {
        return stripos('|' . $type . '|', '|null|') !== false;
    }

    public static function removeNullable(?string $type): ?string
    {
        if ($type === null) {
            return null;
        }
        return substr(
            str_ireplace('|null|', '|', '|' . $type . '|'),
            1,
            -1
        );
    }

    public static function parseAnnotations(string $docblock): array
    {
        $annotations = [];
        // Strip away the docblock header and footer
        // to ease parsing of one line annotations
        $docblock = substr($docblock, 3, -2);

        $re = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';
        if (preg_match_all($re, $docblock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
    }

    public static function isArrayOfType(string $strType): bool
    {
        return substr($strType, -2) === '[]';
    }
}

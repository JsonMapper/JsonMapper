<?php

declare(strict_types=1);

namespace JsonMapper\Helpers;

use JsonMapper\ValueObjects\AnnotationMap;

class DocBlockHelper
{
    private const PATTERN = '/@(?P<annotation>[A-Za-z_-]+)[ \t]+(?P<type>\??[\w\[\]\\\\|]*)[ \t]?\$?(?P<name>[\w\[\]\\\\|]*)/m';

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
}

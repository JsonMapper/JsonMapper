<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Helpers;

use DannyVanDerSluijs\JsonMapper\Parser\UseNodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class UseStatementHelper
{
    public static function getImports(\ReflectionClass $class): array
    {
        $visitor = new UseNodeVisitor();

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(file_get_contents($class->getFileName()));

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->getImports();
    }
}

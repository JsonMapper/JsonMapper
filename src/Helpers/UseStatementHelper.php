<?php

declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Helpers;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;

class UseStatementHelper
{
    public static function getImports(\ReflectionClass $class): array
    {
        $visitor = new class extends NodeVisitorAbstract {
            /** @var array|string[] */
            private $imports = [];

            public function enterNode(Node $node)
            {
                if ($node instanceof Stmt\Use_) {
                    foreach ($node->uses as $use) {
                        $this->imports[] = '\\' . $use->name;
                    }
                } elseif ($node instanceof Stmt\GroupUse) {
                    foreach ($node->uses as $use) {
                        $this->imports[] = $node->prefix . '\\' . $use;
                    }
                }
            }

            public function getImports(): array
            {
                return $this->imports;
            }
        };

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse(file_get_contents($class->getFileName()));

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->getImports();
    }
}

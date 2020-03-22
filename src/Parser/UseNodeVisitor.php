<?php declare(strict_types=1);

namespace DannyVanDerSluijs\JsonMapper\Parser;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt;

class UseNodeVisitor extends NodeVisitorAbstract
{
    /** @var array|string[] */
    private $imports = [];

    public function enterNode(Node $node): void
    {
        if ($node instanceof Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->imports[] = '\\' . $use->name;
            }
        } elseif ($node instanceof Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->imports[] = $node->prefix . '\\' . $use->name;
            }
        }
    }

    public function getImports(): array
    {
        return $this->imports;
    }
};
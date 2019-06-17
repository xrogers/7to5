<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class YieldArrayAccess extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\ArrayDimFetch) {
            return;
        }
        if (!$node->var instanceof Node\Expr\Yield_) {
            return;
        }
        $node->var = new Node\Expr\FuncCall(new Node\Name('\\returnMe'), [$node->var]);
        return $node;
    }
}

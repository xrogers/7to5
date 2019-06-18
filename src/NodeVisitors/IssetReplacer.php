<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ClassConstFetch;

class IssetReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\Isset_) {
            return;
        }
        foreach ($node->vars as &$var) {
            if (!$var instanceof ArrayDimFetch) continue;
            if (!$var->var instanceof ClassConstFetch) continue;
            $var->var = new Node\Expr\FuncCall(new Node\Name('\\returnMe'), [$var->var]);
        }
        return $node;
    }
}

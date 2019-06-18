<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class YieldMethodCallReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return;
        }
        //Node\Expr\ArrayDimFetch
        $value = &$node->var;
        if (!$value instanceof Node\Expr\Yield_) {
            return;
        }
        $value = new Node\Expr\FuncCall(new Node\Name('\\returnMe'), [$value]);
        return $node;
    }
}

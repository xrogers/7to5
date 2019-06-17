<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class YieldSpecialReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\Yield_) {
            return;
        }
        //Node\Expr\ArrayDimFetch
        $value = &$node->value;
        if ($value instanceof Node\Expr\Variable && $value->name !== "this") {
            return;
        }

        if ($value instanceof Node\Expr\FuncCall ||
            $value instanceof Node\Expr\StaticCall ||
            $value instanceof Node\Scalar
        ) {
            return;
        }
        if ($value instanceof Node\Expr\MethodCall && $value->var instanceof Node\Expr\Variable
        ) {
            return;
        }
        $value = new Node\Expr\FuncCall(new Node\Name('\\returnMe'), [$value]);

        return $node;
    }
}

<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/*
 * Converts define() arrays into const arrays
 */

class ArrayListReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\Assign) {
            return;
        }
        if (!$node->var instanceof Node\Expr\Array_) {
            return;
        }
        $node->var = new Node\Expr\List_($node->var->items);
    }
}

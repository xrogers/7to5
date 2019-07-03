<?php

namespace danog\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/*
 * Converts define() arrays into const arrays
 */

class ArrayListForeachReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Foreach_) {
            return;
        }
        if (!$node->valueVar instanceof Node\Expr\Array_) {
            return;
        }
        $node->valueVar = new Node\Expr\List_($node->valueVar->items);
    }
}

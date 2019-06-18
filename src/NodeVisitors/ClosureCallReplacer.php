<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class ClosureCallReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\FuncCall) {
            return;
        }
        $name = $node->name;
        if ($name instanceof Node\Name) {
            return;
        }
        if ($name instanceof Node\Expr\Variable) {
            return;
        }
        $new_args = array_merge([$name], $node->args);
        return new Node\Expr\FuncCall(new Node\Name('\\callMe'), $new_args);
    }
}

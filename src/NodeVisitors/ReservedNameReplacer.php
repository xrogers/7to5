<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ReservedNameReplacer extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Expr\MethodCall &&
            !$node instanceof Node\Expr\StaticCall &&
            !$node instanceof Node\Stmt\ClassMethod &&
            !$node instanceof Node\Expr\ClassConstFetch &&
            !$node instanceof Node\Const_
        ) {
            return;
        }
        $name = &$node->name;
        if (!is_string($name) || !in_array(strtolower($name), ['continue', 'empty', 'use', 'default', 'echo'])) {
            return;
        }
        $name .= '_';
    }
}

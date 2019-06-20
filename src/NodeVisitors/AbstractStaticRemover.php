<?php

namespace Spatie\Php7to5\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\ClassMethod;

class AbstractStaticRemover extends NodeVisitorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return;
        }
        if ($node->isAbstract() && $node->isStatic()) {
            return NodeTraverser::REMOVE_NODE;
        }
    }
}
